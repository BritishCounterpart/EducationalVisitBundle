<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Approval;

use App\Entity\User;
use Cis\EducationalVisitBundle\CommandBus\Approval\ApproveCommand;
use Cis\EducationalVisitBundle\CommandBus\Approval\ApproveHandler;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Messenger\EducationalVisitMessenger;
use Cis\EmpoweringEnterpriseBundle\Tests\TestCase;
use Prophecy\Argument;

class ApproveHandlerTest extends TestCase
{
    public function testHandle()
    {
        $visit = $this->prophesize(Visit::class);
        $visit->setStatus(Argument::exact(Visit::STATUS_APPROVED))->shouldBeCalled();
        $visit = $visit->reveal();

        $user = $this->prophesize(User::class)->reveal();

        $command = new ApproveCommand($visit, $user);

        $messenger = $this->prophesize(EducationalVisitMessenger::class);
        $messenger->sendVisitApprovedEmail(
            Argument::exact($user),
            Argument::exact($visit)
        )->shouldBeCalled();
        $messenger = $messenger->reveal();

        $handler = new ApproveHandler($messenger);
        $handler->handle($command);
    }
}