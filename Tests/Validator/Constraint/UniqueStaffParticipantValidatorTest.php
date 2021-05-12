<?php

namespace Cis\EducationalVisitBundle\Tests\Validator\Constraint;

use App\Entity\Employee\Employee;
use Cis\EducationalVisitBundle\CommandBus\Participant\AddStaffParticipantCommand;
use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Repository\StaffParticipantRepository;
use Cis\EducationalVisitBundle\Validator\Constraint\UniqueStaffParticipant;
use Cis\EducationalVisitBundle\Validator\Constraint\UniqueStaffParticipantValidator;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use Prophecy\Argument;
use Symfony\Component\Validator\Context\ExecutionContext;

class UniqueStaffParticipantValidatorTest extends TestCase
{
    private $visit;
    private $employee;
    private $command;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class)->reveal();
        $this->employee = $this->prophesize(Employee::class)->reveal();
        $command = $this->prophesize(AddStaffParticipantCommand::class);
        $command->getVisit()->willReturn($this->visit);
        $command = $command->reveal();
        $command->employee = $this->employee;
        $this->command = $command;
    }

    private function createOrm()
    {
        return $this->prophesize(Orm::class);
    }

    public function testValidateExistingParticipant()
    {
        $orm = $this->createOrm();
        $staffParticipant = $this->prophesize(StaffParticipant::class)->reveal();

        $repo = $this->prophesize(StaffParticipantRepository::class);
        $repo->findOneByEmployeeAndVisit(Argument::exact($this->employee), Argument::exact($this->visit))->willReturn($staffParticipant);
        $orm->getRepository(StaffParticipant::class)->willReturn($repo->reveal());

        $validator = new UniqueStaffParticipantValidator($orm->reveal());
        $constraint = new UniqueStaffParticipant();

        $context = $this->prophesize(ExecutionContext::class);
        $context->addViolation(Argument::exact($constraint->message))->shouldBeCalledOnce();

        $validator->initialize($context->reveal());
        $validator->validate($this->command, $constraint);
    }

    public function testValidateUniqueParticipant()
    {
        $orm = $this->createOrm();

        $repo = $this->prophesize(StaffParticipantRepository::class);
        $repo->findOneByEmployeeAndVisit(Argument::exact($this->employee), Argument::exact($this->visit))->willReturn(null);
        $orm->getRepository(StaffParticipant::class)->willReturn($repo->reveal());

        $validator = new UniqueStaffParticipantValidator($orm->reveal());
        $constraint = new UniqueStaffParticipant();

        $context = $this->prophesize(ExecutionContext::class);
        $context->addViolation(Argument::exact($constraint->message))->shouldNotBeCalled();

        $validator->initialize($context->reveal());
        $validator->validate($this->command, $constraint);
    }
}