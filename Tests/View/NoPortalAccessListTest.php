<?php

namespace Cis\EducationalVisitBundle\Tests\View;

use App\Entity\Student\Contact;
use App\Entity\Student\Student;
use App\Repository\Student\ContactCriteria;
use App\Repository\Student\ContactRepository;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Repository\StudentParticipantRepository;
use Cis\EducationalVisitBundle\View\NoPortalAccessList;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use Petroc\Component\View\TraversableOrmData;
use Prophecy\Argument;

class NoPortalAccessListTest extends TestCase
{
    private $orm;
    private $visit;

    protected function setUp()
    {
        $this->orm = $this->prophesize(Orm::class);
        $this->visit = $this->prophesize(Visit::class)->reveal();
    }

    private function createContact(Student $student)
    {
        $contact = $this->prophesize(Contact::class);
        $contact->getStudent()->willReturn($student);
        return $contact->reveal();
    }


    private function createStudent(int $id)
    {
        $student = $this->prophesize(Student::class);
        $student->getId()->willReturn($id);
        return $student->reveal();
    }

    public function testExtendsTraversableOrmData()
    {
        $this->assertInstanceOf(TraversableOrmData::class, new NoPortalAccessList($this->visit));
    }

    public function testGetItems()
    {
        $visit = $this->visit;

        $studentOne = $this->createStudent(1);
        $studentTwo = $this->createStudent(2);

        $students = [
            1 => $studentOne,
            2 => $studentTwo
        ];

        $contacts = [
            $this->createContact($studentOne),
            $this->createContact($studentOne),
            $this->createContact($studentTwo)
        ];

        $orm = $this->orm;

        $repo = $this->prophesize(StudentParticipantRepository::class);
        $repo->findStudentsByVisit(Argument::exact($visit))->willReturn($students);
        $orm->getRepository(StudentParticipant::class)->willReturn($repo->reveal());

        $repo = $this->prophesize(ContactRepository::class);
        $repo->match(Argument::type(ContactCriteria::class))->willReturn($contacts);
        $orm->getRepository(Contact::class)->willReturn($repo->reveal());

        $list = new NoPortalAccessList($visit);
        $list->initialise($orm->reveal());

        $items = $list->getItems();

        $this->assertSame($students, $items);
    }
}