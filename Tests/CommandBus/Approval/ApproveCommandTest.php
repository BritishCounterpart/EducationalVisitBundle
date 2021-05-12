<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Approval;

use App\Entity\User;
use Cis\EducationalVisitBundle\CommandBus\Approval\ApproveCommand;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;

class ApproveCommandTest extends TestCase
{
    public function testConstructor()
    {
        $visit = $this->prophesize(Visit::class)->reveal();
        $user = $this->prophesize(User::class)->reveal();
        $command = new ApproveCommand($visit, $user);
        $this->assertSame($visit, $command->getVisit());
        $this->assertSame($user, $command->getUser());
    }
}