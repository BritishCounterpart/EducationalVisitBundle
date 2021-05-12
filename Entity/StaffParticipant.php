<?php

namespace Cis\EducationalVisitBundle\Entity;

use App\Entity\Employee\Employee;
use DateTime;

class StaffParticipant
{
    private $id;
    private $createdOn;
    private $visit;
    private $employee;
    private $deletedOn;

    public function __construct(Visit $visit, Employee $employee)
    {
        $this->createdOn = new DateTime;
        $this->visit = $visit;
        $this->employee = $employee;
        $visit->addStaffParticipant($this);
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

    public function getEmployee()
    {
        return $this->employee;
    }
}