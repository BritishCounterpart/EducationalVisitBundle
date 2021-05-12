<?php

namespace Cis\EducationalVisitBundle\CommandBus\Itinerary;

use Cis\EducationalVisitBundle\Entity\Itinerary;
use Petroc\Component\CommandBus\Command;

class DeleteCommand extends Command
{
    private $itinerary;

    public function __construct(Itinerary $itinerary)
    {
        $this->itinerary = $itinerary;
    }

    public function getItinerary()
    {
        return $this->itinerary;
    }
}