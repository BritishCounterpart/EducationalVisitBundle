<?php

namespace Cis\EducationalVisitBundle\Entity;

use DateTime;

class ExcludedStudentParticipant
{
    private $id;
    private $createdOn;
    private $visit;
    private $student;

    public function __construct($visit, $student)
    {
        $this->createdOn = new DateTime;
        $this->visit = $visit;
        $this->student = $student;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    public function getVisit()
    {
        return $this->visit;
    }

    public function getStudent()
    {
        return $this->student;
    }
}