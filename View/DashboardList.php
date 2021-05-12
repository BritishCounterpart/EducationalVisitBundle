<?php

namespace Cis\EducationalVisitBundle\View;

use App\Entity\Employee\Employee;
use App\Entity\Employee\WorkArea;
use App\Entity\User;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Component\View\OrmData;

class DashboardList extends OrmData
{
    private $user;
    private $employee;
    private $area;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getEmployee()
    {
        if(!empty($this->employee)) {
            return $this->employee;
        }

        return $this->employee = $this->getRepository(Employee::class)->findOneByUser($this->user);
    }

    public function getArea()
    {
        if(!empty($this->area)) {
            return $this->area;
        }

        $employee = $this->getEmployee();

        return $this->area = $this->getRepository(WorkArea::class)->findByEmployee($employee);
    }

    public function getUpcomingVisits()
    {
        $employee = $this->getEmployee();
        $area = $this->getArea();
        return $this->getRepository(Visit::class)->findUpcomingByEmployeeAndAreas($employee, $area);
    }

    public function getVisitsRequiringAttention()
    {
        $employee = $this->getEmployee();
        $area = $this->getArea();
        return $this->getRepository(Visit::class)->findIssuesByEmployeeAndAreas($employee, $area);
    }


    public function getVisitsPendingMyApproval()
    {
        return $this->getRepository(Visit::class)->findPendingApprovalVisitByApprover($this->user);
    }

}