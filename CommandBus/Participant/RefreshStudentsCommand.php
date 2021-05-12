<?php

namespace Cis\EducationalVisitBundle\CommandBus\Participant;

use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Component\CommandBus\Command;

class RefreshStudentsCommand extends Command
{
    private $visit;
    
    public function __construct(Visit $visit)
    {
	    $this->visit = $visit;
    }
    
    public function getVisit()
    {
	    return $this->visit;
    }   
}