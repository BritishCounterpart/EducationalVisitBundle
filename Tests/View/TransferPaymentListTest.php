<?php

namespace Cis\EducationalVisitBundle\Tests\View;

use App\Entity\Student\Student;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Repository\StudentParticipantRepository;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Cis\EducationalVisitBundle\View\TransferPaymentList;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use Petroc\Component\View\TraversableOrmData;
use Prophecy\Argument;

class TransferPaymentListTest extends TestCase
{
    private $util;
    private $orm;
    private $student;

    protected function setUp()
    {
        $this->util = $this->prophesize(ParticipantUtil::class);
        $this->orm = $this->prophesize(Orm::class);
        $this->student = $this->prophesize(Student::class);
    }

    private function createStudentParticipantWithVisit(int $id,  int $visitId, bool $noLongerGoing = null, string $evNumber = null)
    {
        $visit = $this->prophesize(Visit::class);
        $visit->getId()->willReturn($visitId);
        $visit->getEvNumber()->willReturn($evNumber);
        $visit = $visit->reveal();
        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getId()->willReturn($id);
        $studentParticipant->getVisit()->willReturn($visit);
        $studentParticipant->isNoLongerGoing()->willReturn($noLongerGoing);
        $studentParticipant->getStudent()->willReturn($this->student->reveal());
        return $studentParticipant->reveal();
    }

    public function testExtendsTraversableOrmData()
    {
        $studentParticipant = $this->createStudentParticipantWithVisit(20, 30, false, 'EV');
        $this->assertInstanceOf(TraversableOrmData::class, new TransferPaymentList($this->util->reveal(), $studentParticipant));
    }

    public function testGetItems()
    {
        $orm = $this->orm;
        $util = $this->util;
        $student = $this->student->reveal();

        $studentParticipant = $this->createStudentParticipantWithVisit(20, 30, false, 'EV');

        // Should be included in util
        $studentParticipant1 = $this->createStudentParticipantWithVisit(1, 10, false, 'EV');
        $studentParticipant2 = $this->createStudentParticipantWithVisit(2, 25, false, 'EV');
        $studentParticipant3 = $this->createStudentParticipantWithVisit(3, 23, false, 'EV');

        // Should be excluded before reaching util
        $studentParticipant4 = $this->createStudentParticipantWithVisit(4, 30, false, 'EV');
        $studentParticipant5 = $this->createStudentParticipantWithVisit(5, 40, true, 'EV');
        $studentParticipant6 = $this->createStudentParticipantWithVisit(6, 40, true);

        $studentParticipants = [
            $studentParticipant1,
            $studentParticipant2,
            $studentParticipant3,
            $studentParticipant4,
            $studentParticipant5,
            $studentParticipant6
        ];

        $repo = $this->prophesize(StudentParticipantRepository::class);
        $repo->findByStudent($student)->willReturn($studentParticipants);
        $orm->getRepository(StudentParticipant::class)->willReturn($repo->reveal());

        // Should include student participant 1 and 2 only in final output
        $util->getStudentPayment(Argument::exact($studentParticipant1))->willReturn(['paid' => false])->shouldBeCalled();
        $util->getStudentPayment(Argument::exact($studentParticipant2))->willReturn(['paid' => false])->shouldBeCalled();
        $util->getStudentPayment(Argument::exact($studentParticipant3))->willReturn(['paid' => true])->shouldBeCalled();

        $list = new TransferPaymentList($util->reveal(), $studentParticipant);

        $list->initialise($orm->reveal());

        $items = $list->getItems();

        $expected = [
            0 => [
                'fromStudentParticipant' => $studentParticipant,
                'toStudentParticipant' => $studentParticipant1,
                'payment' => ['paid' => false]
            ],
            1 => [
                'fromStudentParticipant' => $studentParticipant,
                'toStudentParticipant' => $studentParticipant2,
                'payment' => ['paid' => false]
            ],
        ];

        $this->assertSame($expected, $items);

    }

    public function testGetFromPaymentAmount()
    {
        $amount = 49.87;
        $util = $this->util;
        $studentParticipant = $this->createStudentParticipantWithVisit(20, 30, false, 'EV');

        $util->getStudentPayment(Argument::exact($studentParticipant))->willReturn(['amountPaid' => $amount])->shouldBeCalled();

        $list = new TransferPaymentList($util->reveal(), $studentParticipant);

        $this->assertSame($amount, $list->getFromPaymentAmount());
    }

}