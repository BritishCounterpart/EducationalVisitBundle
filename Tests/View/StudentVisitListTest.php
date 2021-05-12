<?php

namespace Cis\EducationalVisitBundle\Tests\View;

use App\Entity\Student\Student;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Repository\StudentParticipantRepository;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Cis\EducationalVisitBundle\View\StudentVisitList;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use Petroc\Component\View\TraversableOrmData;
use Prophecy\Argument;

class StudentVisitListTest extends TestCase
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

    private function createStudentParticipantWithVisit(bool $noLongerGoing, string $visitStatus)
    {
        $visit = $this->prophesize(Visit::class);
        $visit->getStatus()->willReturn($visitStatus);
        $visit = $visit->reveal();
        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getVisit()->willReturn($visit);
        $studentParticipant->isNoLongerGoing()->willReturn($noLongerGoing);
        return $studentParticipant->reveal();
    }

    private function createList(ParticipantUtil $util, Student $student)
    {
        return new StudentVisitList($util, $student);
    }

    public function testExtendsTraversableOrmData()
    {
        $this->assertInstanceOf(TraversableOrmData::class, new StudentVisitList($this->util->reveal(), $this->student->reveal()));
    }

    public function testGetItems()
    {

        $orm = $this->orm;
        $util = $this->util;
        $student = $this->student->reveal();

        $participantOne = $this->createStudentParticipantWithVisit(false, Visit::STATUS_APPROVED);
        $participantTwo = $this->createStudentParticipantWithVisit(false, Visit::STATUS_APPROVED);
        $participantThree = $this->createStudentParticipantWithVisit(true, Visit::STATUS_APPROVED);
        $participantFour = $this->createStudentParticipantWithVisit(false, Visit::STATUS_PLANNED);

        $participants = [
            $participantOne,
            $participantTwo,
            $participantThree,
            $participantFour
        ];

        $repo = $this->prophesize(StudentParticipantRepository::class);
        $repo->findByStudent(Argument::exact($student))->willReturn($participants);
        $orm->getRepository(StudentParticipant::class)->willReturn($repo->reveal());

        $expected = [
            0 => [
                'studentParticipant' => $participantOne,
                'payment' => ['paid' => true]
            ],
            1 => [
                'studentParticipant' => $participantTwo,
                'payment' => ['paid' => false]
            ]
        ];

        $util->getStudentPayment(Argument::exact($participantOne))->shouldBeCalledOnce()->willReturn($expected[0]['payment']);
        $util->getStudentPayment(Argument::exact($participantTwo))->shouldBeCalledOnce()->willReturn($expected[1]['payment']);

        $list = $this->createList($util->reveal(), $student);
        $list->initialise($orm->reveal());

        $this->assertSame($expected, $list->getItems());
    }

    public function testGetLegacyStudent()
    {
        $legacyStudent = 546788;
        $idNumber = 459677;
        $student = $this->student;
        $student->getIdNumber()->willReturn($idNumber);
        $student = $student->reveal();

        $sql = "SELECT * FROM OPENQUERY(QRCS11_MISDEV,'SELECT OBJECT_ID FROM PERSON WHERE ID_NUMBER = $idNumber') Q";

        $statement = $this->prophesize(Statement::class);
        $statement->execute()->shouldBeCalled();
        $statement->fetchColumn()->shouldBeCalled()->willReturn($legacyStudent);
        $statement = $statement->reveal();
        $connection = $this->prophesize(Connection::class);
        $connection->prepare($sql)->shouldBeCalled()->willReturn($statement);
        $connection = $connection->reveal();

        $orm = $this->orm;
        $orm->getConnection()->willReturn($connection);

        $list = $this->createList($this->util->reveal(), $student);
        $list->initialise($orm->reveal());

        $this->assertSame($legacyStudent, $list->getLegacyStudent());
    }
}