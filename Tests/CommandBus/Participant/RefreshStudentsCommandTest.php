<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Participant;

use Cis\EducationalVisitBundle\CommandBus\Participant\RefreshStudentsCommand;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;

class RefreshStudentsCommandTest extends TestCase
{
    public function testConstructor()
    {
        $visit = $this->prophesize(Visit::class)->reveal();
        $command = new RefreshStudentsCommand($visit);

        $this->assertSame($visit, $command->getVisit());
    }
}