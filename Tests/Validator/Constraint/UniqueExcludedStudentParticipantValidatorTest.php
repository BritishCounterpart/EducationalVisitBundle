<?php

namespace Cis\EducationalVisitBundle\Tests\Validator\Constraint;

use App\Entity\Student\Student;
use Cis\EducationalVisitBundle\CommandBus\Participant\ExcludeStudentCommand;
use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Repository\ExcludedStudentParticipantRepository;
use Cis\EducationalVisitBundle\Validator\Constraint\UniqueExcludedStudentParticipant;
use Cis\EducationalVisitBundle\Validator\Constraint\UniqueExcludedStudentParticipantValidator;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use Prophecy\Argument;
use Symfony\Component\Validator\Context\ExecutionContext;

class UniqueExcludedStudentParticipantValidatorTest extends TestCase
{
    private $visit;
    private $student;
    private $command;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class)->reveal();
        $this->student = $this->prophesize(Student::class)->reveal();
        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getVisit()->willReturn($this->visit);
        $studentParticipant->getStudent()->willReturn($this->student);
        $command = $this->prophesize(ExcludeStudentCommand::class);
        $command->getStudentParticipant()->willReturn($studentParticipant->reveal());
        $this->command = $command->reveal();
    }

    private function createOrm()
    {
        return $this->prophesize(Orm::class);
    }

    public function testValidateExistingParticipant()
    {
        $excludedStudentParticipant = $this->prophesize(ExcludedStudentParticipant::class)->reveal();

        $orm = $this->createOrm();

        $repo = $this->prophesize(ExcludedStudentParticipantRepository::class);
        $repo->findOneByStudentAndVisit(Argument::exact($this->student), Argument::exact($this->visit))->willReturn($excludedStudentParticipant);
        $orm->getRepository(ExcludedStudentParticipant::class)->willReturn($repo->reveal());

        $validator = new UniqueExcludedStudentParticipantValidator($orm->reveal());
        $constraint = new UniqueExcludedStudentParticipant();

        $context = $this->prophesize(ExecutionContext::class);
        $context->addViolation(Argument::exact($constraint->message))->shouldBeCalledOnce();

        $validator->initialize($context->reveal());
        $validator->validate($this->command, $constraint);
    }

    public function testValidateUniqueParticipant()
    {
        $orm = $this->createOrm();

        $repo = $this->prophesize(ExcludedStudentParticipantRepository::class);
        $repo->findOneByStudentAndVisit(Argument::exact($this->student), Argument::exact($this->visit))->willReturn(null);
        $orm->getRepository(ExcludedStudentParticipant::class)->willReturn($repo->reveal());

        $validator = new UniqueExcludedStudentParticipantValidator($orm->reveal());
        $constraint = new UniqueExcludedStudentParticipant();

        $context = $this->prophesize(ExecutionContext::class);
        $context->addViolation(Argument::exact($constraint->message))->shouldNotBeCalled();

        $validator->initialize($context->reveal());
        $validator->validate($this->command, $constraint);
    }
}