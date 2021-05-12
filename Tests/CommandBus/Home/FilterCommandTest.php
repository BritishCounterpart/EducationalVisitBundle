<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Home;

use Cis\EducationalVisitBundle\CommandBus\Home\FilterCommand;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Repository\VisitCriteria;
use Cis\EducationalVisitBundle\Repository\VisitRepository;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\CommandBus\FilterCommandInterface;
use Petroc\Component\Helper\Orm;

class FilterCommandTest extends TestCase
{
    public function testConstructor()
    {
        $command = new FilterCommand();
        $this->assertInstanceOf(FilterCommandInterface::class, $command);
        $this->assertInstanceOf(VisitCriteria::class, $command);
    }

    public function testHandle()
    {
        $command = new FilterCommand();

        $result = ['France Trip'];

        $repo = $this->prophesize(VisitRepository::class);

        $repo->match($command)->willReturn($result);

        $orm = $this->prophesize(Orm::class);
        $orm->getRepository(Visit::class)->willReturn($repo->reveal());

        $command->handle($orm->reveal());
        $this->assertSame($result, $command->getResult());
    }
}