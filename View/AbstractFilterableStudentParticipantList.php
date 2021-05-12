<?php

namespace Cis\EducationalVisitBundle\View;

use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Petroc\Component\View\OrmData;

abstract class AbstractFilterableStudentParticipantList extends OrmData
{
    const PAYMENT_STATUS_PAID = 'Paid';
    const PAYMENT_STATUS_PARTIAL_PAYMENT = 'Partial Payment';
    const PAYMENT_STATUS_REFUNDED = 'Refunded';
    const PAYMENT_STATUS_PARTIAL_REFUND = 'Partial Refund';
    const PAYMENT_STATUS_NOT_PAID = 'Not Paid';

    protected $util;
    protected $visit;
    protected $studentParticipants;
    public $paymentStatus;
    public $showNoLongerGoing = false;

    public function __construct(ParticipantUtil $util, Visit $visit)
    {
        $this->util = $util;
        $this->visit = $visit;
    }

    public function getStudentParticipants()
    {
        if(null !== $studentParticipants = $this->studentParticipants) {
            return $studentParticipants;
        }

        $studentParticipants = $this->getRepository(StudentParticipant::class)->findByVisit($this->visit);

        return $this->studentParticipants = $studentParticipants;
    }

    public function getStudentParticipantsPayments()
    {
        $list = [];

        $studentParticipants = $this->getStudentParticipants();

        foreach($studentParticipants as $studentParticipant) {
            $payment = $this->util->getStudentPayment($studentParticipant);
            $payment = $this->paymentStatusFilter($studentParticipant, $payment);
            // null is where payment status criteria has not been met
            if($payment !== null) {
                if ($this->showNoLongerGoing === false) {
                    if ($studentParticipant->isNoLongerGoing() === false) {
                        $list[] = $payment;
                    }
                } else {
                    $list[] = $payment;
                }
            }
        }

        return $list;
    }

    private function paymentStatusFilter(StudentParticipant $studentParticipant, array $payment)
    {
        $paymentStatus = $this->paymentStatus;

        if($paymentStatus === null) {
            return [
                'studentParticipant' => $studentParticipant,
                'payment' => $payment,
            ];
        } elseif($paymentStatus === self::PAYMENT_STATUS_PAID) {
            if($payment['paid'] === true) {
                return [
                    'studentParticipant' => $studentParticipant,
                    'payment' => $payment,
                ];
            } else {
                return null;
            }
        } elseif($paymentStatus === self::PAYMENT_STATUS_PARTIAL_PAYMENT) {
            if($payment['paid'] === false and $payment['amountPaid'] > 0.0) {
                return [
                    'studentParticipant' => $studentParticipant,
                    'payment' => $payment,
                ];
            } else {
                return null;
            }
        } elseif($paymentStatus === self::PAYMENT_STATUS_REFUNDED) {
            if ($payment['paid'] === false and $payment['amountPaid'] < 0.01 and $payment['refunds'] > 0.0) {
                return [
                    'studentParticipant' => $studentParticipant,
                    'payment' => $payment,
                ];
            } else {
                return null;
            }
        } elseif($paymentStatus === self::PAYMENT_STATUS_PARTIAL_REFUND) {
            if ($payment['paid'] === false and $payment['amountPaid'] > 0.0 and $payment['refunds'] > 0.0) {
                return [
                    'studentParticipant' => $studentParticipant,
                    'payment' => $payment,
                ];
            } else {
                return null;
            }
        } elseif($paymentStatus === self::PAYMENT_STATUS_NOT_PAID) {
            if ($payment['paid'] === false and $payment['amountPaid'] < 0.01) {
                return [
                    'studentParticipant' => $studentParticipant,
                    'payment' => $payment,
                ];
            } else {
                return null;
            }
        }

        return null;
    }
}