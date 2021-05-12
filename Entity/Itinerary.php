<?php

namespace Cis\EducationalVisitBundle\Entity;

use DateTime;

class Itinerary
{
    const FILE_DIR = 'educational_visit_itinerary';

    private $id;
    private $createdOn;
    private $visit;
    private $fileLocation;
    private $deletedOn;

    public function __construct(Visit $visit, string $fileLocation)
    {
        $this->createdOn = new DateTime;
        $this->visit = $visit;
        $this->fileLocation = $fileLocation;
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

    public function getFileLocation()
    {
        return $this->fileLocation;
    }

    public function setFileLocation(string $fileLocation)
    {
        $this->fileLocation = $fileLocation;
        return $this;
    }
}