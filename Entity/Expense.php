<?php

namespace Cis\EducationalVisitBundle\Entity;

use DateTime;

class Expense
{
    // Validation
    const MAX_LENGTH_DESCRIPTION = 250;

    private $id;
    private $createdOn;
    private $visit;
    private $type;
    private $description;
    private $amount;
    private $deletedOn;

    public function __construct(Visit $visit, string $type, string $description, float $amount)
    {
        $this->createdOn = new DateTime;
        $this->visit = $visit;
        $this->type = $type;
        $this->description = $description;
        $this->amount = $amount;
        $visit->addExpense($this);
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

    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount(float $amount)
    {
        $this->amount = $amount;
        return $this;
    }
}