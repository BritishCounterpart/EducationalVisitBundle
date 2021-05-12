<?php

namespace Cis\EducationalVisitBundle\Tests\Entity;

use Cis\EducationalVisitBundle\Entity\Itinerary;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;
use DateTime;

class ItineraryTest extends TestCase
{
    public function testConstants()
    {
        $this->assertSame('educational_visit_itinerary', Itinerary::FILE_DIR);
    }

    public function testConstructor()
    {
        $visit = $this->prophesize(Visit::class)->reveal();
        $fileLocation = 'location';

        $itinerary = new Itinerary($visit, $fileLocation);

        $this->assertNull($itinerary->getId());
        $this->assertInstanceOf(DateTime::class, $itinerary->getCreatedOn());
        $this->assertSame($visit, $itinerary->getVisit());
        $this->assertSame($fileLocation, $itinerary->getFileLocation());
    }

    public function testSetFileLocation()
    {
        $visit = $this->prophesize(Visit::class)->reveal();
        $fileLocation = 'location';

        $itinerary = new Itinerary($visit, $fileLocation);

        $this->assertSetAndGet($itinerary, 'fileLocation', 'new location');
    }

}