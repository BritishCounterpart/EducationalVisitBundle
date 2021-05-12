<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Payment;

use Cis\EducationalVisitBundle\CommandBus\Payment\ChangeStudentParticipantPaymentAmountCommand;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Petroc\Bridge\PhpUnit\TestCase;
use Prophecy\Argument;

class ChangeStudentParticipantPaymentAmountCommandTest extends TestCase
{
    private $studentParticipant;

    protected function setUp()
    {
        $this->studentParticipant = $this->prophesize(StudentParticipant::class);
    }

    public function testConstructor()
    {
        $fullPaymentAmount = 30.45;
        $firstPaymentAmount = 20.00;
        $studentParticipant = $this->studentParticipant;
        $studentParticipant->getFullPaymentAmount()->willReturn($fullPaymentAmount);
        $studentParticipant->getFirstPaymentAmount()->willReturn($firstPaymentAmount);
        $studentParticipant = $studentParticipant->reveal();

        $command = new ChangeStudentParticipantPaymentAmountCommand($studentParticipant);

        $this->assertSame($fullPaymentAmount, $command->fullPaymentAmount);
        $this->assertSame($firstPaymentAmount, $command->firstPaymentAmount);
    }

    public function testHandle()
    {
        $fullPaymentAmount = 40.45;
        $firstPaymentAmount = 10.00;
        $studentParticipant = $this->studentParticipant;
        $studentParticipant->getFullPaymentAmount()->willReturn(null);
        $studentParticipant->getFirstPaymentAmount()->willReturn(null);
        $studentParticipant->setFullPaymentAmount(Argument::exact($fullPaymentAmount))->shouldBeCalledOnce();
        $studentParticipant->setFirstPaymentAmount(Argument::exact($firstPaymentAmount))->shouldBeCalledOnce();
        $studentParticipant = $studentParticipant->reveal();

        $command = new ChangeStudentParticipantPaymentAmountCommand($studentParticipant);
        $command->fullPaymentAmount = $fullPaymentAmount;
        $command->firstPaymentAmount = $firstPaymentAmount;

        $command->handle();
    }
}