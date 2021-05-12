<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Approval;

use App\Entity\User;
use Cis\EducationalVisitBundle\CommandBus\Approval\RejectCommand;
use Cis\EducationalVisitBundle\CommandBus\Approval\RejectHandler;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Messenger\EducationalVisitMessenger;
use Cis\EmpoweringEnterpriseBundle\Tests\TestCase;
use Prophecy\Argument;

class RejectHandlerTest extends TestCase
{
    public function testHandle()
    {
        $reason = 'Needs more details';

        $visit = $this->prophesize(Visit::class);
        $visit->setStatus(Argument::exact(Visit::STATUS_NOT_APPROVED))->shouldBeCalled();
        $visit = $visit->reveal();

        $user = $this->prophesize(User::class)->reveal();

        $command = new RejectCommand($visit, $user);
        $command->reason = $reason;

        $messenger = $this->prophesize(EducationalVisitMessenger::class);
        $messenger->sendVisitRejectedEmail(
            Argument::exact($user),
            Argument::exact($visit),
            Argument::exact($reason)
        )->shouldBeCalled();
        $messenger = $messenger->reveal();

        $handler = new RejectHandler($messenger);
        $handler->handle($command);
    }
}