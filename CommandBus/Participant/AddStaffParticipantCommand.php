<?php

namespace Cis\EducationalVisitBundle\CommandBus\Participant;

use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Validator\Constraint\UniqueStaffParticipant;
use Petroc\Component\CommandBus\Command;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class AddStaffParticipantCommand extends Command
{
    private $visit;
    private $staffParticipant;
    public $employee;

    public function __construct(Visit $visit)
    {
        $this->visit = $visit;
    }

    public function getVisit()
    {
        return $this->visit;
    }

    public function getStaffParticipant()
    {
        return $this->staffParticipant;
    }

    public function setStaffParticipant(StaffParticipant $staffParticipant)
    {
        $this->staffParticipant = $staffParticipant;
        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('employee', new NotBlank());
        $metadata->addConstraint(new UniqueStaffParticipant());
    }
}