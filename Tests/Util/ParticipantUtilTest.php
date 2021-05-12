<?php

namespace Cis\EducationalVisitBundle\Tests\Util;

use App\Cohort\ProviderManager;
use App\Entity\Cohort\Cohort;
use App\Entity\Order\Item;
use App\Entity\Order\Order;
use App\Entity\PersonalRecord\Note;
use App\Entity\PersonalRecord\Option;
use App\Entity\Student\Student;
use App\Repository\Order\OrderCriteria;
use App\Repository\Order\OrderRepository;
use App\Repository\PersonalRecord\NoteRepository;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use Prophecy\Argument;

class ParticipantUtilTest extends TestCase
{
    private $orm;
    private $provider;
    private $cohort;

    protected function setUp()
    {
        $this->orm = $this->prophesize(Orm::class)->reveal();
        $this->provider = $this->prophesize(ProviderManager::class)->reveal();
        $this->cohort = $this->prophesize(Cohort::class)->reveal();
    }

    private function createUtil(Orm $orm, ProviderManager $provider)
    {
        return new ParticipantUtil($orm, $provider);
    }

    public function testGetCohortStudents()
    {
        $this->markTestSkipped('Query is declared "final" and cannot be mocked');
    }

    public function testAddStudents()
    {
        $orm = $this->prophesize(Orm::class);

        // Excluded students will not be added
        $orm->persist(Argument::type(StudentParticipant::class))->shouldBeCalledTimes(2);

        $util = $this->createUtil($orm->reveal(), $this->provider);

        $uniqueStudentOne = $this->prophesize(Student::class);
        $uniqueStudentOne->getId()->willReturn(1);
        $uniqueStudentOne = $uniqueStudentOne->reveal();

        $uniqueStudentTwo = $this->prophesize(Student::class);
        $uniqueStudentTwo->getId()->willReturn(2);
        $uniqueStudentTwo = $uniqueStudentTwo->reveal();

        $duplicateStudent = $this->prophesize(Student::class);
        $duplicateStudent->getId()->willReturn(3);
        $duplicateStudent = $duplicateStudent->reveal();

        $cohortStudents = [
            $uniqueStudentOne,
            $uniqueStudentTwo,
            $duplicateStudent
        ];

        $excludedStudents = [
            $duplicateStudent
        ];

        $visit = $this->prophesize(Visit::class)->reveal();

        $util->addStudents($visit, $this->cohort, $cohortStudents, $excludedStudents);
    }

    public function testRemoveStudent()
    {
        // Participants with no payments will be removed rather than being marked as no longer going
        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->setNoLongerGoing(Argument::any())->shouldNotBeCalled();
        $studentParticipant = $studentParticipant->reveal();
        $orm = $this->prophesize(Orm::class);
        $orm->remove(Argument::exact($studentParticipant))->shouldBeCalledOnce();
        $util = $this->createUtil($orm->reveal(), $this->provider);

        $studentPayment = ['amountPaid' => 0.0];

        $util->removeStudent($studentParticipant, $studentPayment);

        // Participants with payments will not be removed and will be marked as no longer going
        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->setNoLongerGoing(Argument::exact(true))->shouldBeCalledOnce();
        $studentParticipant = $studentParticipant->reveal();
        $orm = $this->prophesize(Orm::class);
        $orm->remove(Argument::exact($studentParticipant))->shouldNotBeCalled();
        $util = $this->createUtil($orm->reveal(), $this->provider);

        $studentPayment = ['amountPaid' => 10.0];

        $util->removeStudent($studentParticipant, $studentPayment);
    }

    public function testGetStudentPayment()
    {
        $requiredAmount =  150.0;
        $visit = $this->prophesize(Visit::class);
        $visit->getFullPaymentAmount()->willReturn($requiredAmount);
        $visit = $visit->reveal();

        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getId()->willReturn(10)->shouldBeCalled();
        $studentParticipant->getFullPaymentAmount()->willReturn(null)->shouldBeCalled();
        $studentParticipant->getVisit()->willReturn($visit);
        $studentParticipant = $studentParticipant->reveal();

        $orm = $this->prophesize(Orm::class);

        // Order classed as paid
        $paidItemOne = $this->prophesize(Item::class);
        $paidItemOne->getStatus()->willReturn(Order::STATUS_PAID);
        $paidItemOne->getAmount()->willReturn(30.0);
        $paidItemOne = $paidItemOne->reveal();

        $paidItemTwo = $this->prophesize(Item::class);
        $paidItemTwo->getStatus()->willReturn(Order::STATUS_PARTIAL_REFUND);
        $paidItemTwo->getAmount()->willReturn(10.0);
        $paidItemTwo = $paidItemTwo->reveal();

        $paidItemThree = $this->prophesize(Item::class);
        $paidItemThree->getStatus()->willReturn(Order::STATUS_AWAITING_REFUND);
        $paidItemThree->getAmount()->willReturn(20.0);
        $paidItemThree = $paidItemThree->reveal();

        $paidItems = [
            $paidItemOne,
            $paidItemTwo,
            $paidItemThree
        ];

        // Order classed as refunded
        $paidOrder = $this->prophesize(Order::class);
        $paidOrder->getItems()->willReturn($paidItems);
        $paidOrder = $paidOrder->reveal();

        $refundedItemOne = $this->prophesize(Item::class);
        $refundedItemOne->getStatus()->willReturn(Order::STATUS_PARTIAL_REFUND);
        $refundedItemOne->getAmount()->willReturn(-5.0);
        $refundedItemOne = $refundedItemOne->reveal();

        $refundedItemTwo = $this->prophesize(Item::class);
        $refundedItemTwo->getStatus()->willReturn(Order::STATUS_REFUNDED);
        $refundedItemTwo->getAmount()->willReturn(-10.5);
        $refundedItemTwo = $refundedItemTwo->reveal();


        $refundedItems = [
            $refundedItemOne,
            $refundedItemTwo
        ];

        $refundedOrder = $this->prophesize(Order::class);
        $refundedOrder->getItems()->willReturn($refundedItems);
        $refundedOrder = $refundedOrder->reveal();

        // Create Orders
        $orders = [
            $paidOrder,
            $refundedOrder
        ];

        $repo = $this->prophesize(OrderRepository::class);
        $repo->match(Argument::type(OrderCriteria::class))->willReturn($orders);
        $orm->getRepository(Order::class)->willReturn($repo->reveal());

        $util = $this->createUtil($orm->reveal(), $this->provider);
        $studentPayment = $util->getStudentPayment($studentParticipant);

        $payment = [
            'paid' => false,
            'requiredAmount' => $requiredAmount,
            'payments' => 50.0,
            'refunds' => 25.5,
            'amountPaid' => 24.5,
            'remainingAmount' => 125.5
        ];

        $this->assertSame($payment, $studentPayment);
    }

    public function testHasConfirmed()
    {
        $util = $this->createUtil($this->orm, $this->provider);

        $payment = ['amountPaid' => 5];
        $this->assertTrue($util->hasConfirmed($payment));
        $payment = ['amountPaid' => 0];
        $this->assertFalse($util->hasConfirmed($payment));
    }


    public function testIsAbleToGo()
    {
        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->isNoLongerGoing()->willReturn(false);
        $studentParticipant->hasVisitConsent()->willReturn('Yes');
        $studentParticipant = $studentParticipant->reveal();

        $util = $this->createUtil($this->orm, $this->provider);

        $isAbleToGo = $util->isAbleToGo($studentParticipant, [], ['paid' => true]);
        $this->assertTrue($isAbleToGo);
    }

    public function testIsAbleToGoNoLongerGoing()
    {
        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->isNoLongerGoing()->willReturn(true);
        $studentParticipant->hasVisitConsent()->willReturn('Yes');
        $studentParticipant = $studentParticipant->reveal();

        $util = $this->createUtil($this->orm, $this->provider);

        $isAbleToGo = $util->isAbleToGo($studentParticipant, [], ['paid' => true]);
        $this->assertFalse($isAbleToGo);
    }

    public function testIsAbleToGoVisitConsent()
    {
        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->isNoLongerGoing()->willReturn(false);
        $studentParticipant->hasVisitConsent()->willReturn('No');
        $studentParticipant = $studentParticipant->reveal();

        $util = $this->createUtil($this->orm, $this->provider);

        $isAbleToGo = $util->isAbleToGo($studentParticipant, [], ['paid' => true]);
        $this->assertFalse($isAbleToGo);
    }

    public function testIsAbleToGoOsaConsent()
    {
        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->isNoLongerGoing()->willReturn(false);
        $studentParticipant->hasVisitConsent()->willReturn('Yes');
        $studentParticipant = $studentParticipant->reveal();

        $util = $this->createUtil($this->orm, $this->provider);

        $isAbleToGo = $util->isAbleToGo($studentParticipant, ['Student'], ['paid' => true]);
        $this->assertFalse($isAbleToGo);
    }

    public function testIsAbleToGoPaid()
    {
        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->isNoLongerGoing()->willReturn(false);
        $studentParticipant->hasVisitConsent()->willReturn('Yes');
        $studentParticipant = $studentParticipant->reveal();

        $util = $this->createUtil($this->orm, $this->provider);

        $isAbleToGo = $util->isAbleToGo($studentParticipant, [], ['paid' => false]);
        $this->assertFalse($isAbleToGo);
    }

    public function testgetMissingOSAConsentUnder18NoConsent()
    {
        $orm = $this->prophesize(Orm::class);

        $student = $this->prophesize(Student::class);
        $student->getAgeToday()->willReturn(17);
        $student = $student->reveal();

        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getId()->willReturn(20);
        $studentParticipant->getStudent()->willReturn($student);
        $studentParticipant = $studentParticipant->reveal();

        $repo = $this->prophesize(NoteRepository::class);
        $repo->findOneOsaConsentByStudent(Argument::exact($student))->willReturn(null);
        $orm->getRepository(Note::class)->willReturn($repo->reveal());

        $util = $this->createUtil($orm->reveal(), $this->provider);
        $consent = $util->getMissingOSAConsent($studentParticipant);

        $this->assertCount(2, $consent);
        $this->assertContains('Missing Parent Consent', $consent);
        $this->assertContains('Missing Student Consent', $consent);

        // Test cache
        $consent = $util->getMissingOSAConsent($studentParticipant);
        $this->assertCount(2, $consent);
    }

    public function testgetMissingOSAConsent18OrOverNoConsent()
    {
        $orm = $this->prophesize(Orm::class);

        $student = $this->prophesize(Student::class);
        $student->getAgeToday()->willReturn(18);
        $student = $student->reveal();

        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getId()->willReturn(20);
        $studentParticipant->getStudent()->willReturn($student);
        $studentParticipant = $studentParticipant->reveal();

        $repo = $this->prophesize(NoteRepository::class);
        $repo->findOneOsaConsentByStudent(Argument::exact($student))->willReturn(null);
        $orm->getRepository(Note::class)->willReturn($repo->reveal());

        $util = $this->createUtil($orm->reveal(), $this->provider);

        $consent = $util->getMissingOSAConsent($studentParticipant);
        $this->assertCount(1, $consent);
        $this->assertContains('Missing Student Consent', $consent);

        // Test cache
        $consent = $util->getMissingOSAConsent($studentParticipant);
        $this->assertCount(1, $consent);
    }

    public function testgetMissingOSAConsentUnder18HasConsent()
    {
        $orm = $this->prophesize(Orm::class);

        $student = $this->prophesize(Student::class);
        $student->getAgeToday()->willReturn(17);
        $student = $student->reveal();

        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getId()->willReturn(20);
        $studentParticipant->getStudent()->willReturn($student);
        $studentParticipant = $studentParticipant->reveal();

        $note = $this->prophesize(Note::class);
        $note->hasOption(Argument::exact(Option::OSA_CONSENT_STUDENT_SIGNED_ID))->willReturn(true);
        $note->hasOption(Argument::exact(Option::OSA_CONSENT_PARENT_SIGNED_ID))->willReturn(true);
        $note = $note->reveal();

        $repo = $this->prophesize(NoteRepository::class);
        $repo->findOneOsaConsentByStudent(Argument::exact($student))->willReturn($note);
        $orm->getRepository(Note::class)->willReturn($repo->reveal());

        $util = $this->createUtil($orm->reveal(), $this->provider);
        $consent = $util->getMissingOSAConsent($studentParticipant);

        $this->assertCount(0, $consent);

        // Test cache
        $consent = $util->getMissingOSAConsent($studentParticipant);
        $this->assertCount(0, $consent);
    }

    public function testgetMissingOSAConsentUnder18NoParentConsent()
    {
        $orm = $this->prophesize(Orm::class);

        $student = $this->prophesize(Student::class);
        $student->getAgeToday()->willReturn(17);
        $student = $student->reveal();

        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getId()->willReturn(20);
        $studentParticipant->getStudent()->willReturn($student);
        $studentParticipant = $studentParticipant->reveal();

        $note = $this->prophesize(Note::class);
        $note->hasOption(Argument::exact(Option::OSA_CONSENT_STUDENT_SIGNED_ID))->willReturn(true);
        $note->hasOption(Argument::exact(Option::OSA_CONSENT_PARENT_SIGNED_ID))->willReturn(false);
        $note = $note->reveal();

        $repo = $this->prophesize(NoteRepository::class);
        $repo->findOneOsaConsentByStudent(Argument::exact($student))->willReturn($note);
        $orm->getRepository(Note::class)->willReturn($repo->reveal());

        $util = $this->createUtil($orm->reveal(), $this->provider);
        $consent = $util->getMissingOSAConsent($studentParticipant);

        $this->assertCount(1, $consent);
        $this->assertContains('Missing Parent Consent', $consent);

        // Test cache
        $consent = $util->getMissingOSAConsent($studentParticipant);
        $this->assertCount(1, $consent);
    }

    public function testgetMissingOSAConsentUnder18NoStudentConsent()
    {
        $orm = $this->prophesize(Orm::class);

        $student = $this->prophesize(Student::class);
        $student->getAgeToday()->willReturn(17);
        $student = $student->reveal();

        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getId()->willReturn(20);
        $studentParticipant->getStudent()->willReturn($student);
        $studentParticipant = $studentParticipant->reveal();

        $note = $this->prophesize(Note::class);
        $note->hasOption(Argument::exact(Option::OSA_CONSENT_STUDENT_SIGNED_ID))->willReturn(false);
        $note->hasOption(Argument::exact(Option::OSA_CONSENT_PARENT_SIGNED_ID))->willReturn(true);
        $note = $note->reveal();

        $repo = $this->prophesize(NoteRepository::class);
        $repo->findOneOsaConsentByStudent(Argument::exact($student))->willReturn($note);
        $orm->getRepository(Note::class)->willReturn($repo->reveal());

        $util = $this->createUtil($orm->reveal(), $this->provider);
        $consent = $util->getMissingOSAConsent($studentParticipant);

        $this->assertCount(1, $consent);
        $this->assertContains('Missing Student Consent', $consent);

        // Test cache
        $consent = $util->getMissingOSAConsent($studentParticipant);
        $this->assertCount(1, $consent);
    }

    public function testgetMissingOSAConsent18OrOverNoStudentConsent()
    {
        $orm = $this->prophesize(Orm::class);

        $student = $this->prophesize(Student::class);
        $student->getAgeToday()->willReturn(18);
        $student = $student->reveal();

        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getId()->willReturn(20);
        $studentParticipant->getStudent()->willReturn($student);
        $studentParticipant = $studentParticipant->reveal();

        $note = $this->prophesize(Note::class);
        $note->hasOption(Argument::exact(Option::OSA_CONSENT_STUDENT_SIGNED_ID))->willReturn(false);
        $note->hasOption(Argument::exact(Option::OSA_CONSENT_PARENT_SIGNED_ID))->willReturn(true);
        $note = $note->reveal();

        $repo = $this->prophesize(NoteRepository::class);
        $repo->findOneOsaConsentByStudent(Argument::exact($student))->willReturn($note);
        $orm->getRepository(Note::class)->willReturn($repo->reveal());

        $util = $this->createUtil($orm->reveal(), $this->provider);
        $consent = $util->getMissingOSAConsent($studentParticipant);

        $this->assertCount(1, $consent);
        $this->assertContains('Missing Student Consent', $consent);

        // Test cache
        $consent = $util->getMissingOSAConsent($studentParticipant);
        $this->assertCount(1, $consent);
    }

}