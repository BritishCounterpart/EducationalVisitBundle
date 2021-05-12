<?php

namespace Cis\EducationalVisitBundle\Validator\Constraint;

use Cis\EducationalVisitBundle\CommandBus\Participant\AddStaffParticipantCommand;
use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Petroc\Component\Helper\Orm;
use Petroc\Component\Util\AssertTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueStaffParticipantValidator extends ConstraintValidator
{
    use AssertTrait;
    private $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function validate($command, Constraint $constraint)
    {
        $this->assertInstanceOf($command, [
            AddStaffParticipantCommand::class
        ]);

        $staffParticipant = $this->orm
            ->getRepository(StaffParticipant::class)
            ->findOneByEmployeeAndVisit(
                $command->employee,
                $command->getVisit()
            );

        if($staffParticipant !== null) {
            $this->context->addViolation($constraint->message);
        }

    }
}