<?php

namespace Cis\EducationalVisitBundle\View;

use App\Entity\Student\Contact;
use App\Repository\Student\ContactCriteria;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Component\View\TraversableOrmData;

class NoPortalAccessList extends TraversableOrmData
{
    private $visit;

    public function __construct(Visit $visit)
    {
        $this->visit = $visit;
    }

    public function getItems()
    {
        $students = $this->getRepository(StudentParticipant::class)->findStudentsByVisit($this->visit);

        $criteria = new ContactCriteria();
        $criteria->students = $students;
        $criteria->studentPortalUser = false;

        $contacts = $this->getRepository(Contact::class)->match($criteria);

        // Create unique array of students
        $students = [];
        foreach ($contacts as $contact) {
            $student = $contact->getStudent();
            $students[$student->getId()] = $student;
        }

        return $students;
    }
}