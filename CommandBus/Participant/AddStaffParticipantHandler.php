<?php

namespace Cis\EducationalVisitBundle\CommandBus\Participant;

use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Petroc\Component\CommandBus\HandlerInterface;
use Petroc\Component\Helper\Orm;

class AddStaffParticipantHandler implements HandlerInterface
{
    private $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function handle(AddStaffParticipantCommand $command)
    {
        $orm = $this->orm;

        $staffParticipant = new StaffParticipant(
            $command->getVisit(),
            $command->employee
        );

        $orm->persist($staffParticipant);

        $command->setStaffParticipant($staffParticipant);
    }
}