<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Approval;

use Cis\EducationalVisitBundle\CommandBus\Approval\RequestCommand;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;
use Prophecy\Argument;

class RequestCommandTest extends TestCase
{

    public function testHandle()
    {
        $visit = $this->prophesize(Visit::class);
        $visit->setStatus(Argument::exact(Visit::STATUS_PENDING_APPROVAL))->shouldBeCalledOnce();

        $command = new RequestCommand($visit->reveal());
        $command->handle();
    }

}