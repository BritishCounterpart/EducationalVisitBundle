<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Itinerary;

use Cis\EducationalVisitBundle\CommandBus\Itinerary\UploadCommand;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;

class UploadCommandTest extends TestCase
{
    public function testConstructor()
    {
        $visit = $this->prophesize(Visit::class)->reveal();
        $command = new UploadCommand($visit);
        $this->assertSame($visit, $command->getVisit());
    }

    public function testLoadValidatorMetadata()
    {
        $this->assertCanLoadValidatorMetadata(UploadCommand::class);
    }
}