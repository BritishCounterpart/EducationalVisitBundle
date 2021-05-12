<?php

namespace Cis\EducationalVisitBundle\View;

use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Petroc\Component\View\TraversableOrmData;

class TransferPaymentList extends TraversableOrmData
{
    private $util;
    private $studentParticipant;

    public function __construct(ParticipantUtil $util, StudentParticipant $studentParticipant)
    {
        $this->util = $util;
        $this->studentParticipant = $studentParticipant;
    }

    public function getItems()
    {
        $fromStudentParticipant = $this->studentParticipant;
        $student = $fromStudentParticipant->getStudent();
        $visit = $fromStudentParticipant->getVisit();

        $studentParticipants = $this->getRepository(StudentParticipant::class)->findByStudent($student);

        $include = [];

        foreach($studentParticipants as $toStudentParticipant) {
            if($toStudentParticipant->getVisit()->getEvNumber() !== null and $toStudentParticipant->isNoLongerGoing() === false and $toStudentParticipant->getVisit()->getId() !== $visit->getId()) {
                $payment = $this->util->getStudentPayment($toStudentParticipant);
                if($payment['paid'] === false) {
                    $include[] = [
                        'fromStudentParticipant' => $fromStudentParticipant,
                        'toStudentParticipant' => $toStudentParticipant,
                        'payment' => $payment
                    ];
                }
            }
        }

        return $include;
    }

    public function getFromPaymentAmount()
    {
        return $this->util->getStudentPayment($this->studentParticipant)['amountPaid'];
    }
}