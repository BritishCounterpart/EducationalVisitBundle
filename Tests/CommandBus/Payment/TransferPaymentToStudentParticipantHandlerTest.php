<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Payment;

use App\Util\OrderUtil;
use Cis\EducationalVisitBundle\CommandBus\Payment\TransferPaymentToStudentParticipantCommand;
use Cis\EducationalVisitBundle\CommandBus\Payment\TransferPaymentToStudentParticipantHandler;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EmpoweringEnterpriseBundle\Tests\TestCase;
use Prophecy\Argument;

class TransferPaymentToStudentParticipantHandlerTest extends TestCase
{
    private $util;

    protected function setUp()
    {
        $this->util = $this->prophesize(OrderUtil::class);
    }

    private function createStudentParticipantWithVisit(int $id, string $title, string $costCode = null, string $evNumber = null)
    {
        $visit = $this->prophesize(Visit::class);
        $visit->getTitle()->willReturn($title);
        $visit->getCostCode()->willReturn($costCode);
        $visit->getEvNumber()->willReturn($evNumber);
        $visit = $visit->reveal();
        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getId()->willReturn($id);
        $studentParticipant->getVisit()->willReturn($visit);
        return $studentParticipant->reveal();
    }

    public function testHandle()
    {
        $util = $this->util;

        $fromId = 1;
        $fromCostCode = 'A';
        $fromEvNumber = 'C';
        $toId = 2;
        $toCostCode = 'D';
        $toEvNumber = 'E';

        $util->transferOrder(Argument::withEntry('from_object', $fromId))->shouldBeCalled();
        $util->transferOrder(Argument::withEntry('from_reference', $fromEvNumber))->shouldBeCalled();
        $util->transferOrder(Argument::withEntry('from_cost_code', $fromCostCode))->shouldBeCalled();
        $util->transferOrder(Argument::withEntry('to_object', $toId))->shouldBeCalled();
        $util->transferOrder(Argument::withEntry('to_reference', $toEvNumber))->shouldBeCalled();
        $util->transferOrder(Argument::withEntry('to_cost_code', $toCostCode))->shouldBeCalled();
        $util->transferOrder(Argument::withKey('description'))->shouldBeCalled();

        $fromStudentParticipant = $this->createStudentParticipantWithVisit($fromId, 'EV1', $fromCostCode, $fromEvNumber);
        $toStudentParticipant = $this->createStudentParticipantWithVisit($toId, 'EV2', $toCostCode, $toEvNumber);

        $command = new TransferPaymentToStudentParticipantCommand($fromStudentParticipant, $toStudentParticipant);
        $handler = new TransferPaymentToStudentParticipantHandler($util->reveal());
        $handler->handle($command);
    }
}