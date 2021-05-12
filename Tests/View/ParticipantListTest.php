<?php

namespace Cis\EducationalVisitBundle\Tests\View;

use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Repository\ExcludedStudentParticipantRepository;
use Cis\EducationalVisitBundle\Repository\StaffParticipantRepository;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Cis\EducationalVisitBundle\View\AbstractFilterableStudentParticipantList;
use Cis\EducationalVisitBundle\View\ParticipantList;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use Prophecy\Argument;

class ParticipantListTest extends TestCase
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
        return new ParticipantList($util, $visit);
    }

    private function createVisit()
    {
        return $this->prophesize(Visit::class);
    }

    private function createStaffParticipant()
    {
        return $this->prophesize(StaffParticipant::class);
    }

    private function createExcludedStudentParticipant()
    {
        return $this->prophesize(ExcludedStudentParticipant::class);
    }

    public function testExtendsOrmData()
    {
        $visit = $this->createVisit()->reveal();
        $list = $this->createList($this->util->reveal(), $visit);
        $this->assertInstanceOf(AbstractFilterableStudentParticipantList::class, $list);
    }

    public function testGetStaffParticipants()
    {
        $orm = $this->orm;
        $visit = $this->createVisit()->reveal();

        $participantOne = $this->createStaffParticipant()->reveal();
        $participantTwo = $this->createStaffParticipant()->reveal();
        $participantThree = $this->createStaffParticipant()->reveal();

        $participants = [
            $participantOne,
            $participantTwo,
            $participantThree
        ];

        $repo = $this->prophesize(StaffParticipantRepository::class);
        $repo->findByVisit(Argument::exact($visit))->willReturn($participants);
        $orm->getRepository(StaffParticipant::class)->willReturn($repo->reveal());

        $list = $this->createList($this->util->reveal(), $visit);
        $list->initialise($orm->reveal());

        $this->assertSame($participants, $list->getStaffParticipants());
        // Test Cache
        $this->assertSame($participants, $list->getStaffParticipants());
    }

    public function testGetExcludedStudentParticipants()
    {
        $orm = $this->orm;
        $visit = $this->createVisit()->reveal();

        $participantOne = $this->createExcludedStudentParticipant()->reveal();
        $participantTwo = $this->createExcludedStudentParticipant()->reveal();
        $participantThree = $this->createExcludedStudentParticipant()->reveal();

        $participants = [
            $participantOne,
            $participantTwo,
            $participantThree
        ];

        $repo = $this->prophesize(ExcludedStudentParticipantRepository::class);
        $repo->findByVisit(Argument::exact($visit))->willReturn($participants);
        $orm->getRepository(ExcludedStudentParticipant::class)->willReturn($repo->reveal());

        $list = $this->createList($this->util->reveal(), $visit);
        $list->initialise($orm->reveal());

        $this->assertSame($participants, $list->getExcludedStudentParticipants());
        // Test Cache
        $this->assertSame($participants, $list->getExcludedStudentParticipants());
    }
}