<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Itinerary;

use Cis\EducationalVisitBundle\CommandBus\Itinerary\DeleteCommand;
use Cis\EducationalVisitBundle\CommandBus\Itinerary\DeleteHandler;
use Cis\EducationalVisitBundle\Entity\Itinerary;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use Prophecy\Argument;

class DeleteHandlerTest extends TestCase
{
    private $visit;
    private $itinerary;
    private $orm;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class);
        $this->itinerary = $this->prophesize(Itinerary::class);
        $this->orm = $this->prophesize(Orm::class);
    }

    public function testHandleNewUpload()
    {
        $visit = $this->visit;
        $visit->setItinerary(Argument::exact(null))->shouldBeCalledOnce();
        $visit = $visit->reveal();

        $itinerary = $this->itinerary;
        $itinerary->getVisit()->willReturn($visit);
        $itinerary = $itinerary->reveal();

        $orm = $this->orm;
        $orm->remove(Argument::exact($itinerary))->shouldBeCalledOnce();
        $orm = $orm->reveal();

        $handler = new DeleteHandler($orm);
        $handler->handle(new DeleteCommand($itinerary));
    }
}