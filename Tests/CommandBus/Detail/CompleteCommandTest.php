<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Detail;

use Cis\EducationalVisitBundle\CommandBus\Detail\CompleteCommand;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;
use Prophecy\Argument;

class CompleteCommandTest extends TestCase
{
    public function testHandle()
    {
        $visit = $this->prophesize(Visit::class);
        $visit->setStatus(Argument::exact(Visit::STATUS_COMPLETED))->shouldBeCalledOnce();

        $command = new CompleteCommand($visit->reveal());
        $command->handle();
    }
}