<?php

namespace Cis\EducationalVisitBundle\Tests\View;

use App\Entity\Employee\Employee;
use App\Entity\Employee\WorkArea;
use App\Entity\User;
use App\Repository\Employee\EmployeeRepository;
use App\Repository\Employee\WorkAreaRepository;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Repository\VisitRepository;
use Cis\EducationalVisitBundle\View\DashboardList;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use Petroc\Component\View\OrmData;

class DashboardListTest extends TestCase
{
    private $user;
    private $employee;
    private $workArea;
    private $visit;

    protected function setUp()
    {
        $this->user = $this->prophesize(User::class)->reveal();
        $this->employee = $this->prophesize(Employee::class)->reveal();
        $this->workArea = $this->prophesize(WorkArea::class)->reveal();
        $this->visit = $this->prophesize(Visit::class);
    }

    private function createList()
    {
        return new DashboardList($this->user);
    }

    private function getDefaultOrm()
    {
        $orm = $this->prophesize(Orm::class);

        // Setup employee repository
        $repo = $this->prophesize(EmployeeRepository::class);
        $repo->findOneByUser($this->user)->willReturn($this->employee);
        $orm->getRepository(Employee::class)->willReturn($repo->reveal());

        // Setup work area repository
        $repo = $this->prophesize(WorkAreaRepository::class);
        $repo->findByEmployee($this->employee)->willReturn([$this->workArea]);
        $orm->getRepository(WorkArea::class)->willReturn($repo->reveal());

        return $orm;

    }

    public function testConstructor()
    {
        $list = new DashboardList($this->user);
        $this->assertInstanceOf(OrmData::class, $list);
    }

    public function testGetUpcomingVisits()
    {
        $orm = $this->getDefaultOrm();

        // Setup visit repository
        $repo = $this->prophesize(VisitRepository::class);

        $visits = [
            $this->visit->reveal(),
            $this->visit->reveal(),
            $this->visit->reveal()
        ];

        $repo->findUpcomingByEmployeeAndAreas($this->employee, [$this->workArea])->willReturn($visits);

        $orm->getRepository(Visit::class)->willReturn($repo->reveal());

        $list = $this->createList();
        $list->initialise($orm->reveal());

        $this->assertCount(3, $list->getUpcomingVisits());
    }

    public function testGetVisitsRequiringAttention()
    {
        $orm = $this->getDefaultOrm();

        // Setup visit repository
        $repo = $this->prophesize(VisitRepository::class);

        $visits = [
            $this->visit->reveal(),
            $this->visit->reveal()
        ];

        $repo->findIssuesByEmployeeAndAreas($this->employee, [$this->workArea])->willReturn($visits);

        $orm->getRepository(Visit::class)->willReturn($repo->reveal());

        $list = $this->createList();
        $list->initialise($orm->reveal());

        $this->assertCount(2, $list->getVisitsRequiringAttention());
        // Parameters cached
        $this->assertCount(2, $list->getVisitsRequiringAttention());
    }
}