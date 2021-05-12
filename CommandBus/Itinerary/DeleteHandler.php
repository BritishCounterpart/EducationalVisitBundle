<?php

namespace Cis\EducationalVisitBundle\CommandBus\Itinerary;

use Petroc\Component\CommandBus\HandlerInterface;
use Petroc\Component\Helper\Orm;

class DeleteHandler implements HandlerInterface
{
    private $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function handle(DeleteCommand $command)
    {
        $orm = $this->orm;
        $itinerary = $command->getItinerary();
        $visit = $itinerary->getVisit();
        $visit->setItinerary(null);
        $orm->remove($itinerary);
    }
}