<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Detail;

use Cis\EducationalVisitBundle\CommandBus\Detail\EditFinanceCodesCommand;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EmpoweringEnterpriseBundle\Tests\TestCase;

class EditFinanceCodesCommandTest extends TestCase
{
    private $visit;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class);
    }

    public function testConstructor()
    {
        $visit = $this->visit->reveal();
        $command = new EditFinanceCodesCommand($visit);
        $this->assertSame($visit, $command->getVisit());
    }

    public function testHandle()
    {
        $evNumber = 'DFJ98634';
        $costCode = 'HD-34';
        $visit = $this->prophesize(Visit::class);

        $visit->getEvNumber()->shouldBeCalled();
        $visit->getCostCode()->shouldBeCalled();
        $visit->addFinanceCodes($evNumber, $costCode)->shouldBeCalled();
        $visit = $visit->reveal();

        $command = new EditFinanceCodesCommand($visit);
        $command->evNumber = $evNumber;
        $command->costCode = $costCode;
        $command->handle();
    }

    public function testLoadValidatorMetadata()
    {
        $this->assertCanLoadValidatorMetadata(EditFinanceCodesCommand::class);
    }
}