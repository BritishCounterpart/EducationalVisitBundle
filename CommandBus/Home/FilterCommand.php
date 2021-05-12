<?php

namespace Cis\EducationalVisitBundle\CommandBus\Home;

use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Repository\VisitCriteria;
use Petroc\Component\CommandBus\FilterCommandInterface;
use Petroc\Component\CommandBus\FilterCommandTrait;
use Petroc\Component\Helper\Orm;

class FilterCommand extends VisitCriteria implements FilterCommandInterface
{
    use FilterCommandTrait;

    public function handle(Orm $orm)
    {
        $this->setResult($orm->getRepository(Visit::class)->match($this));
    }
}