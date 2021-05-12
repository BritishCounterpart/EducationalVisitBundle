<?php

namespace Cis\EducationalVisitBundle\CommandBus\Participant;

use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Petroc\Component\CommandBus\HandlerInterface;
use Petroc\Component\Helper\Orm;

class ExcludeStudentHandler implements HandlerInterface
{
    private $orm;
    private $util;

    public function __construct(Orm $orm, ParticipantUtil $util)
    {
        $this->orm = $orm;
        $this->util = $util;
    }

    public function handle(ExcludeStudentCommand $command)
    {
        $util = $this->util;
        $studentParticipant = $command->getStudentParticipant();
        $studentPayment = $util->getStudentPayment($studentParticipant);

        $excludedStudentParticipant = new ExcludedStudentParticipant(
            $studentParticipant->getVisit(),
            $studentParticipant->getStudent()
        );

        $this->orm->persist($excludedStudentParticipant);

        $this->util->removeStudent($studentParticipant, $studentPayment);

        $command->setExcludedStudentParticipant($excludedStudentParticipant);
    }
}