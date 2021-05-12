<?php

namespace Cis\EducationalVisitBundle\CommandBus\Payment;

use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Petroc\Component\CommandBus\SelfHandlingCommand;

class ChangeStudentParticipantPaymentAmountCommand extends SelfHandlingCommand
{
    private $studentParticipant;
    public $fullPaymentAmount;
    public $firstPaymentAmount;

    public function __construct(StudentParticipant $studentParticipant)
    {
        $this->studentParticipant = $studentParticipant;
        $this->fullPaymentAmount = $studentParticipant->getFullPaymentAmount();
        $this->firstPaymentAmount = $studentParticipant->getFirstPaymentAmount();
    }

    public function handle()
    {
        $studentParticipant = $this->studentParticipant;
        $studentParticipant->setFullPaymentAmount($this->fullPaymentAmount);
        $studentParticipant->setFirstPaymentAmount($this->firstPaymentAmount);
    }
}