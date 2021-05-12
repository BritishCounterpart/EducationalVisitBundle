<?php

namespace Cis\EducationalVisitBundle\Tests\Entity;

use App\Entity\User;
use Cis\EducationalVisitBundle\Entity\Area;
use Cis\EducationalVisitBundle\Entity\AreaApprover;
use Petroc\Bridge\PhpUnit\TestCase;

class AreaTest extends TestCase
{
    public function testGetId()
    {
        $area = new Area();
        $value = 1234;
        $this->setPropertyValue($area, 'id', $value);
        $this->assertSame($value, $area->getId());
    }

    public function testGetName()
    {
        $area = new Area();
        $value = 'Department';
        $this->setPropertyValue($area, 'name', $value);
        $this->assertSame($value, $area->getName());
    }

    public function testGetApprovers()
    {
        $area = new Area();
        $value = ['John'];
        $this->setPropertyValue($area, 'approvers', $value);
        $this->assertSame($value, $area->getApprovers());
    }

    public function testToString()
    {
        $area = new Area();
        $value = 'Department';
        $this->setPropertyValue($area, 'name', $value);
        $this->assertSame($value, $area->__toString());
    }


    public function testGetApprovalUsers()
    {
        $user = $this->prophesize(User::class)->reveal();
        $user2 = $this->prophesize(User::class)->reveal();

        $users = [
            $user,
            $user2
        ];

        $approver = $this->prophesize(AreaApprover::class);
        $approver->getUser()->willReturn($user);
        $approver = $approver->reveal();

        $approver2 = $this->prophesize(AreaApprover::class);
        $approver2->getUser()->willReturn($user2);
        $approver2 = $approver2->reveal();

        $approvers = [
            $approver,
            $approver2
        ];

        $area = new Area();
        $this->setPropertyValue($area, 'approvers', $approvers);
        $this->assertSame($users, $area->getApprovalUsers());
    }
}