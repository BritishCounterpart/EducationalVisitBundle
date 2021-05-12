<?php

namespace Cis\EducationalVisitBundle\Tests\Entity;

use App\Entity\Employee\Employee;
use App\Entity\User;
use Cis\EducationalVisitBundle\Entity\Area;
use Cis\EducationalVisitBundle\Entity\AreaApprover;
use Petroc\Bridge\PhpUnit\TestCase;

class AreaApproverTest extends TestCase
{
    public function testGetId()
    {
        $areaApprover = new AreaApprover();
        $value = 1234;
        $this->setPropertyValue($areaApprover, 'id', $value);
        $this->assertSame($value, $areaApprover->getId());
    }

    public function testGetArea()
    {
        $areaApprover = new AreaApprover();
        $value = $this->prophesize(Area::class)->reveal();
        $this->setPropertyValue($areaApprover, 'area', $value);
        $this->assertSame($value, $areaApprover->getArea());
    }

    public function testGetEmployee()
    {
        $areaApprover = new AreaApprover();
        $value = $this->prophesize(Employee::class)->reveal();
        $this->setPropertyValue($areaApprover, 'employee', $value);
        $this->assertSame($value, $areaApprover->getEmployee());
    }

    public function testGetUser()
    {
        $areaApprover = new AreaApprover();
        $value = $this->prophesize(User::class)->reveal();
        $this->setPropertyValue($areaApprover, 'user', $value);
        $this->assertSame($value, $areaApprover->getUser());
    }
}