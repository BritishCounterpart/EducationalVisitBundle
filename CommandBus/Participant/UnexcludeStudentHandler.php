<?php

namespace Cis\EducationalVisitBundle\CommandBus\Participant;

use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Petroc\Component\CommandBus\HandlerInterface;
use Petroc\Component\Helper\Orm;

class UnexcludeStudentHandler implements HandlerInterface
{
    private $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function handle(UnexcludeStudentCommand $command)
    {
        $orm = $this->orm;
        $excludedStudentParticipant = $command->getExcludedStudentParticipant();

        $student = $excludedStudentParticipant->getStudent();
        $visit = $excludedStudentParticipant->getVisit();

        // If they're already a participant but flagged as not going due to being excluded, set them as going
        $studentParticipant = $orm->getRepository(StudentParticipant::class)->findOneByStudentAndVisit($student, $visit);

        if($studentParticipant !== null) {
            $studentParticipant->setNoLongerGoing(false);
        }

        $orm->remove($excludedStudentParticipant);
    }
}