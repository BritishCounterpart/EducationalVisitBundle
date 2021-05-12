<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Detail;

use Cis\EducationalVisitBundle\CommandBus\Detail\CancelCommand;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;
use Prophecy\Argument;

class CancelCommandTest extends TestCase
{
    public function testHandle()
    {
        $visit = $this->prophesize(Visit::class);
        $visit->setStatus(Argument::exact(Visit::STATUS_CANCELLED))->shouldBeCalledOnce();

        $command = new CancelCommand($visit->reveal());
        $command->handle();
    }
}