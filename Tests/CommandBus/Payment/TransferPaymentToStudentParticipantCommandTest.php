<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Payment;

use Cis\EducationalVisitBundle\CommandBus\Payment\TransferPaymentToStudentParticipantCommand;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EmpoweringEnterpriseBundle\Tests\TestCase;

class TransferPaymentToStudentParticipantCommandTest extends TestCase
{
    private $studentParticipant;

    protected function setUp()
    {
        $this->studentParticipant = $this->prophesize(StudentParticipant::class);
    }

    public function testConstructor()
    {
        $fromStudentParticipant = $this->studentParticipant->reveal();
        $toStudentParticipant = $this->studentParticipant->reveal();
        $command = new TransferPaymentToStudentParticipantCommand($fromStudentParticipant, $toStudentParticipant);

        $this->assertSame($fromStudentParticipant, $command->getFromStudentParticipant());
        $this->assertSame($toStudentParticipant, $command->getToStudentParticipant());
    }
}