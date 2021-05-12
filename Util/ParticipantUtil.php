<?php

namespace Cis\EducationalVisitBundle\Util;

use App\Cohort\ProviderManager;
use App\Cohort\StudentProvider;
use App\Entity\Cohort\Cohort;
use App\Entity\Order\Order;
use App\Entity\Order\Payment;
use App\Entity\PersonalRecord\Note;
use App\Entity\PersonalRecord\Option;
use App\Repository\Order\OrderCriteria;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Component\Helper\Orm;

class ParticipantUtil
{
    private $orm;
    private $providerManager;
    private $missingOsaConsent = [];

    public function __construct(Orm $orm, ProviderManager $providerManager)
    {
        $this->orm = $orm;
        $this->providerManager = $providerManager;
    }

    public function getCohortStudents(Cohort $cohort)
    {
        return $this->providerManager
            ->getProvider(StudentProvider::ID)
            ->getResult($this->orm, $cohort)
        ;
    }

    public function addStudents(Visit $visit, Cohort $cohort, array $cohortStudents, array $excludedStudents)
    {
        $orm = $this->orm;

        foreach($cohortStudents as $cohortStudent) {
            if(!in_array($cohortStudent, $excludedStudents)) {
                $participant = new StudentParticipant($visit, $cohortStudent);
                $participant->setCohort($cohort);
                $orm->persist($participant);
            }
        }

        return $this;
    }

    public function removeStudent(StudentParticipant $studentParticipant, array $studentPayment)
    {
        if($studentPayment['amountPaid'] > 0) {
            $studentParticipant->setNoLongerGoing(true);
        } else {
            $this->orm->remove($studentParticipant);
        }

        return $this;
    }

    public function getStudentPayment(StudentParticipant $studentParticipant)
    {
        $requiredAmount = $studentParticipant->getFullPaymentAmount();

        if($requiredAmount === null) {
            $requiredAmount = $studentParticipant->getVisit()->getFullPaymentAmount();
        }

        $criteria = new OrderCriteria();
        $criteria->referenceNumber = $studentParticipant->getId();
        $criteria->paymentStatus = Payment::STATUS_OK;
        $orders = $this->orm->getRepository(Order::class)->match($criteria);

        $payments = 0.0;
        $refunds = 0.0;

        foreach($orders as $order) {
            foreach($order->getItems() as $item) {
                if(in_array($item->getStatus(), [Order::STATUS_PAID, Order::STATUS_AWAITING_PARTIAL, Order::STATUS_AWAITING_REFUND])) {
                    $payments += $item->getAmount();
                }
                if(in_array($item->getStatus(), [Order::STATUS_PARTIAL_REFUND, Order::STATUS_REFUNDED])) {
                    $refunds += abs($item->getAmount());
                }
            }
        }

        $amountPaid = $payments - $refunds;

        return [
            'paid' => $amountPaid >= $requiredAmount,
            'requiredAmount' => $requiredAmount,
            'payments' => $payments,
            'refunds' => $refunds,
            'amountPaid' => $amountPaid,
            'remainingAmount' => $requiredAmount - $amountPaid
        ];
    }

    public function hasConfirmed(array $payment)
    {
        return $payment['amountPaid'] > 0.01;
    }

    public function isAbleToGo(StudentParticipant $studentParticipant, array $missingOsaConsent, array $payment)
    {
        if($studentParticipant->isNoLongerGoing() === false and $studentParticipant->hasVisitConsent() !== 'No' and count($missingOsaConsent) < 1 and $payment['paid'] === true) {
            return true;
        }

        return false;
    }

    public function getMissingOSAConsent(StudentParticipant $studentParticipant)
    {
        // Check cache to prevent being re-queried if called again
        $id = $studentParticipant->getId();
        if(array_key_exists($id, $this->missingOsaConsent)) {
            return $this->missingOsaConsent[$id];
        }

        $age = $studentParticipant->getStudent()->getAgeToday();
        $missingParentConsent = 'Missing Parent Consent';
        $missingStudentConsent = 'Missing Student Consent';

        $consent = $this->orm
            ->getRepository(Note::class)
            ->findOneOsaConsentByStudent(
                $studentParticipant->getStudent()
            );

        if($consent === null and $age < 18) {
            return $this->missingOsaConsent[$id] = [
                $missingStudentConsent,
                $missingParentConsent
            ];
        }

        if($consent === null and $age > 17) {
            return $this->missingOsaConsent[$id] = [
                $missingStudentConsent
            ];
        }

        $hasConsent = [];

        if(!$consent->hasOption(Option::OSA_CONSENT_STUDENT_SIGNED_ID)) {
            $hasConsent[] = $missingStudentConsent;
        }

        if(!$consent->hasOption(Option::OSA_CONSENT_PARENT_SIGNED_ID) and  $age < 18) {
            $hasConsent[] = $missingParentConsent;
        }

        // Cache to prevent being re-queried
        return $this->missingOsaConsent[$id] = $hasConsent;
    }
}