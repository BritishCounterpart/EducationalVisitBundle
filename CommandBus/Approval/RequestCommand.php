<?php

namespace Cis\EducationalVisitBundle\CommandBus\Approval;

use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Component\CommandBus\SelfHandlingCommand;

class RequestCommand extends SelfHandlingCommand
{
    private $visit;

    public function __construct(Visit $visit)
    {
        $this->visit = $visit;
    }

    public function handle()
    {
        $this->visit->setStatus(Visit::STATUS_PENDING_APPROVAL);
    }
}