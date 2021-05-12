<?php

namespace Cis\EducationalVisitBundle\CommandBus\Payment;

use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Petroc\Component\CommandBus\Command;

class TransferPaymentToStudentParticipantCommand extends Command
{
    private $fromStudentParticipant;
    private $toStudentParticipant;

    public function __construct(StudentParticipant $fromStudentParticipant, StudentParticipant $toStudentParticipant)
    {
        $this->fromStudentParticipant = $fromStudentParticipant;
        $this->toStudentParticipant = $toStudentParticipant;
    }

    public function getFromStudentParticipant()
    {
        return $this->fromStudentParticipant;
    }

    public function getToStudentParticipant()
    {
        return $this->toStudentParticipant;
    }
}