<?php

namespace Cis\EducationalVisitBundle\Entity;

class AreaApprover
{
    private $id;
    private $area;
    private $employee;
    private $user;

    public function getId()
    {
        return $this->id;
    }

    public function getArea()
    {
        return $this->area;
    }

    public function getEmployee()
    {
        return $this->employee;
    }

    public function getUser()
    {
        return $this->user;
    }
}