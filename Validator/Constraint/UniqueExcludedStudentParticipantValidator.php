<?php

namespace Cis\EducationalVisitBundle\Validator\Constraint;

use Cis\EducationalVisitBundle\CommandBus\Participant\ExcludeStudentCommand;
use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Petroc\Component\Helper\Orm;
use Petroc\Component\Util\AssertTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueExcludedStudentParticipantValidator extends ConstraintValidator
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
            ExcludeStudentCommand::class
        ]);

        $studentParticipant = $command->getStudentParticipant();
        $student = $studentParticipant->getStudent();
        $visit = $studentParticipant->getVisit();
        $excludedStudentParticipant = $this->orm
            ->getRepository(ExcludedStudentParticipant::class)
            ->findOneByStudentAndVisit(
                $student,
                $visit
            );

        if($excludedStudentParticipant !== null) {
            $this->context->addViolation($constraint->message);
        }

    }

}