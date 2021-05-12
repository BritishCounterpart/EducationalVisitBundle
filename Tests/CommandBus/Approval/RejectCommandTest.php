<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Approval;

use App\Entity\User;
use Cis\EducationalVisitBundle\CommandBus\Approval\RejectCommand;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;

class RejectCommandTest extends TestCase
{
    public function testConstructor()
    {
        $visit = $this->prophesize(Visit::class)->reveal();
        $user = $this->prophesize(User::class)->reveal();
        $command = new RejectCommand($visit, $user);
        $this->assertSame($visit, $command->getVisit());
        $this->assertSame($user, $command->getUser());
    }

    public function testLoadValidatorMetadata()
    {
        $this->assertCanLoadValidatorMetadata(RejectCommand::class);
    }
}