<?php

namespace Cis\EducationalVisitBundle\CommandBus\Participant;

use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Petroc\Component\CommandBus\Command;

class UnexcludeStudentCommand extends Command
{
    private $excludedStudentParticipant;

    public function __construct(ExcludedStudentParticipant $excludedStudentParticipant)
    {
        $this->excludedStudentParticipant = $excludedStudentParticipant;
    }

    public function getExcludedStudentParticipant()
    {
        return $this->excludedStudentParticipant;
    }
}