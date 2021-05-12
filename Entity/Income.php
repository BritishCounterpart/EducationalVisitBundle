<?php

namespace Cis\EducationalVisitBundle\Entity;

use DateTime;

class Income
{
    // Validation
    const MAX_LENGTH_INCOME_OTHER_FROM = 500;

    private $id;
    private $createdOn;
    private $visit;
    private $incomeStudent = 0.0;
    private $incomeCollege = 0.0;
    private $incomeOther = 0.0;
    private $incomeOtherFrom;
    private $deletedOn;

    public function __construct(Visit $visit)
    {
        $this->createdOn = new DateTime;
        $this->visit = $visit;
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

    public function getIncomeStudent()
    {
        return $this->incomeStudent;
    }

    public function setIncomeStudent(float $incomeStudent)
    {
        $this->incomeStudent = $incomeStudent;
        return $this;
    }

    public function getIncomeCollege()
    {
        return $this->incomeCollege;
    }

    public function setIncomeCollege(float $incomeCollege)
    {
        $this->incomeCollege = $incomeCollege;
        return $this;
    }

    public function getIncomeOther()
    {
        return $this->incomeOther;
    }

    public function setIncomeOther(float $incomeOther)
    {
        $this->incomeOther = $incomeOther;
        return $this;
    }

    public function getIncomeOtherFrom()
    {
        return $this->incomeOtherFrom;
    }

    public function setIncomeOtherFrom(string $incomeOtherFrom = null)
    {
        $this->incomeOtherFrom = $incomeOtherFrom;
        return $this;
    }
}