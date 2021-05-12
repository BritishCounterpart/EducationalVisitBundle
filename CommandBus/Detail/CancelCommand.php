<?php

namespace Cis\EducationalVisitBundle\CommandBus\Detail;

use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Component\CommandBus\SelfHandlingCommand;

class CancelCommand extends SelfHandlingCommand
{
    private $visit;

    public function __construct(Visit $visit)
    {
        $this->visit = $visit;
    }

    public function handle()
    {
        $this->visit->setStatus(Visit::STATUS_CANCELLED);
    }
}