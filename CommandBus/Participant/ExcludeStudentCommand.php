<?php

namespace Cis\EducationalVisitBundle\CommandBus\Participant;

use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Validator\Constraint\UniqueExcludedStudentParticipant;
use Petroc\Component\CommandBus\Command;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class ExcludeStudentCommand extends Command
{
    private $studentParticipant;
    private $excludedStudentParticipant;

    public function __construct(StudentParticipant $studentParticipant)
    {
        $this->studentParticipant = $studentParticipant;
    }

    public function getStudentParticipant()
    {
        return $this->studentParticipant;
    }

    public function getExcludedStudentParticipant()
    {
        return $this->excludedStudentParticipant;
    }

    public function setExcludedStudentParticipant(ExcludedStudentParticipant $excludedStudentParticipant)
    {
        $this->excludedStudentParticipant = $excludedStudentParticipant;
        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueExcludedStudentParticipant());
    }
}