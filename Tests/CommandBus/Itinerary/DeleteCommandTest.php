<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Itinerary;

use Cis\EducationalVisitBundle\CommandBus\Itinerary\DeleteCommand;
use Cis\EducationalVisitBundle\Entity\Itinerary;
use Petroc\Bridge\PhpUnit\TestCase;

class DeleteCommandTest extends TestCase
{
    public function testConstructor()
    {
        $itinerary = $this->prophesize(Itinerary::class)->reveal();
        $command = new DeleteCommand($itinerary);
        $this->assertSame($itinerary, $command->getItinerary());
    }

}