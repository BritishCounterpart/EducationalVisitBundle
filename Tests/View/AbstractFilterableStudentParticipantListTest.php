<?php

namespace Cis\EducationalVisitBundle\Tests\View;

use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Repository\StudentParticipantRepository;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Cis\EducationalVisitBundle\View\AbstractFilterableStudentParticipantList;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use Petroc\Component\View\OrmData;
use Prophecy\Argument;

class TestAbstractFilterableStudentParticipantListClass extends AbstractFilterableStudentParticipantList
{
    public function __construct(ParticipantUtil $util, Visit $visit)
    {
        parent::__construct($util, $visit);
    }
}

class AbstractFilterableStudentParticipantListTest extends TestCase
{
    private $orm;
    private $util;

    protected function setUp()
    {
        $this->orm = $this->prophesize(Orm::class);
        $this->util = $this->prophesize(ParticipantUtil::class);
    }

    private function createList(ParticipantUtil $util, Visit $visit)
    {
        return new TestAbstractFilterableStudentParticipantListClass($util, $visit);
    }

    private function createVisit()
    {
        return $this->prophesize(Visit::class);
    }

    private function createStudentParticipant()
    {
        return $this->prophesize(StudentParticipant::class);
    }

    public function testExtendsOrmData()
    {
        $visit = $this->createVisit()->reveal();
        $list = $this->createList($this->util->reveal(), $visit);
        $this->assertInstanceOf(OrmData::class, $list);
    }

    public function testGetStudentParticipants()
    {
        $orm = $this->orm;
        $visit = $this->createVisit()->reveal();

        $participantOne = $this->createStudentParticipant()->reveal();
        $participantTwo = $this->createStudentParticipant()->reveal();

        $participants = [
            $participantOne,
            $participantTwo
        ];

        $repo = $this->prophesize(StudentParticipantRepository::class);
        $repo->findByVisit(Argument::exact($visit))->willReturn($participants);
        $orm->getRepository(StudentParticipant::class)->willReturn($repo->reveal());

        $list = $this->createList($this->util->reveal(), $visit);
        $list->initialise($orm->reveal());

        $this->assertSame($participants, $list->getStudentParticipants());
        // Test Cache
        $this->assertSame($participants, $list->getStudentParticipants());
    }


    public function testGetStudentParticipantsPayments()
    {
        $orm = $this->orm;
        $visit = $this->createVisit()->reveal();

        // Paid
        $paidParticipant = $this->createStudentParticipant();
        $paidParticipant->isNoLongerGoing()->willReturn(false);
        $paidParticipant = $paidParticipant->reveal();
        $payment = [
            'paid' => true,
            'refunds' => 0.0,
            'amountPaid' => 20.0
        ];
        $this->util->getStudentPayment(Argument::exact($paidParticipant))->willReturn($payment);
        $paidParticipantPayment = [
            'studentParticipant' => $paidParticipant,
            'payment' => $payment,
        ];

        // No longer going
        $noLongerGoingParticipant = $this->createStudentParticipant();
        $noLongerGoingParticipant->isNoLongerGoing()->willReturn(true);
        $noLongerGoingParticipant = $noLongerGoingParticipant->reveal();
        $payment = [
            'paid' => true,
            'refunds' => 0.0,
            'amountPaid' => 20.0
        ];
        $this->util->getStudentPayment(Argument::exact($noLongerGoingParticipant))->willReturn($payment);
        $noLongerGoingParticipantPayment = [
            'studentParticipant' => $noLongerGoingParticipant,
            'payment' => $payment,
        ];

        // Partial Payment
        $partialPaymentParticipant = $this->createStudentParticipant();
        $partialPaymentParticipant->isNoLongerGoing()->willReturn(false);
        $partialPaymentParticipant = $partialPaymentParticipant->reveal();
        $payment = [
            'paid' => false,
            'refunds' => 0.0,
            'amountPaid' => 10.0
        ];
        $this->util->getStudentPayment(Argument::exact($partialPaymentParticipant))->willReturn($payment);
        $partialPaymentParticipantPayment = [
            'studentParticipant' => $partialPaymentParticipant,
            'payment' => $payment,
        ];

        // Refunded
        $refundedParticipant = $this->createStudentParticipant();
        $refundedParticipant->isNoLongerGoing()->willReturn(false);
        $refundedParticipant = $refundedParticipant->reveal();
        $payment = [
            'paid' => false,
            'refunds' => 20.0,
            'amountPaid' => 0.0
        ];
        $this->util->getStudentPayment(Argument::exact($refundedParticipant))->willReturn($payment);
        $refundedParticipantPayment = [
            'studentParticipant' => $refundedParticipant,
            'payment' => $payment,
        ];

        // Partial Refund
        $partialRefundParticipant = $this->createStudentParticipant();
        $partialRefundParticipant->isNoLongerGoing()->willReturn(false);
        $partialRefundParticipant = $partialRefundParticipant->reveal();
        $payment = [
            'paid' => false,
            'refunds' => 20.0,
            'amountPaid' => 10.0
        ];
        $this->util->getStudentPayment(Argument::exact($partialRefundParticipant))->willReturn($payment);
        $partialRefundParticipantPayment = [
            'studentParticipant' => $partialRefundParticipant,
            'payment' => $payment,
        ];

        // Not Paid
        $notPaidParticipant = $this->createStudentParticipant();
        $notPaidParticipant->isNoLongerGoing()->willReturn(false);
        $notPaidParticipant = $notPaidParticipant->reveal();
        $payment = [
            'paid' => false,
            'refunds' => 0.0,
            'amountPaid' => 0.0
        ];
        $this->util->getStudentPayment(Argument::exact($notPaidParticipant))->willReturn($payment);
        $notPaidParticipantPayment = [
            'studentParticipant' => $notPaidParticipant,
            'payment' => $payment,
        ];

        $participants = [
            $paidParticipant,
            $noLongerGoingParticipant,
            $partialPaymentParticipant,
            $refundedParticipant,
            $partialRefundParticipant,
            $notPaidParticipant
        ];

        $repo = $this->prophesize(StudentParticipantRepository::class);
        $repo->findByVisit(Argument::exact($visit))->willReturn($participants);
        $orm->getRepository(StudentParticipant::class)->willReturn($repo->reveal());

        $list = $this->createList($this->util->reveal(), $visit);
        $list->initialise($orm->reveal());

        // Show only going participants and payment status all
        $list->showNoLongerGoing = false;

        $expected = [
            $paidParticipantPayment,
            $partialPaymentParticipantPayment,
            $refundedParticipantPayment,
            $partialRefundParticipantPayment,
            $notPaidParticipantPayment
        ];

        $this->assertSame($expected, $list->getStudentParticipantsPayments(), 'Show only going');

        // Include not going participants and payment status all
        $list->showNoLongerGoing = true;

        $expected = [
            $paidParticipantPayment,
            $noLongerGoingParticipantPayment,
            $partialPaymentParticipantPayment,
            $refundedParticipantPayment,
            $partialRefundParticipantPayment,
            $notPaidParticipantPayment
        ];

        $this->assertSame($expected, $list->getStudentParticipantsPayments(), 'Include not going');

        // Show only going participants and payment status Paid
        $list->showNoLongerGoing = false;
        $list->paymentStatus = AbstractFilterableStudentParticipantList::PAYMENT_STATUS_PAID;

        $expected = [
            $paidParticipantPayment
        ];

        $this->assertSame($expected, $list->getStudentParticipantsPayments(), AbstractFilterableStudentParticipantList::PAYMENT_STATUS_PAID);

        // Show only going participants and payment status Partial Payment
        $list->showNoLongerGoing = false;
        $list->paymentStatus = AbstractFilterableStudentParticipantList::PAYMENT_STATUS_PARTIAL_PAYMENT;

        $expected = [
            $partialPaymentParticipantPayment,
            $partialRefundParticipantPayment
        ];

        $this->assertSame($expected, $list->getStudentParticipantsPayments(), AbstractFilterableStudentParticipantList::PAYMENT_STATUS_PARTIAL_PAYMENT);

        // Show only going participants and payment status Refunded
        $list->showNoLongerGoing = false;
        $list->paymentStatus = AbstractFilterableStudentParticipantList::PAYMENT_STATUS_REFUNDED;

        $expected = [
            $refundedParticipantPayment
        ];

        $this->assertSame($expected, $list->getStudentParticipantsPayments(), AbstractFilterableStudentParticipantList::PAYMENT_STATUS_REFUNDED);

        // Show only going participants and payment status Partial Refund
        $list->showNoLongerGoing = false;
        $list->paymentStatus = AbstractFilterableStudentParticipantList::PAYMENT_STATUS_PARTIAL_REFUND;

        $expected = [
            $partialRefundParticipantPayment
        ];

        $this->assertSame($expected, $list->getStudentParticipantsPayments(), AbstractFilterableStudentParticipantList::PAYMENT_STATUS_PARTIAL_REFUND);

        // Show only going participants and payment status Not Paid
        $list->showNoLongerGoing = false;
        $list->paymentStatus = AbstractFilterableStudentParticipantList::PAYMENT_STATUS_NOT_PAID;

        $expected = [
            $refundedParticipantPayment,
            $notPaidParticipantPayment
        ];

        $this->assertSame($expected, $list->getStudentParticipantsPayments(), AbstractFilterableStudentParticipantList::PAYMENT_STATUS_NOT_PAID);

        // Show only going participants and payment status Not Paid
        $list->showNoLongerGoing = false;
        $list->paymentStatus = 'fkfuks';

        $this->assertCount(0, $list->getStudentParticipantsPayments(), 'Invalid payment status');
    }

}