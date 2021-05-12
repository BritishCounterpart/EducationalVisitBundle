<?php

namespace Cis\EducationalVisitBundle\Tests\Repository;

use App\Entity\Employee\Employee;
use App\Entity\User;
use App\Tests\EmptyEntityTableFixture;
use App\Tests\EntityRepositoryTestCase;
use App\Tests\Fixture\Employee\EmployeeTableFixture;
use App\Tests\Fixture\User\UserTableFixture;
use Cis\EducationalVisitBundle\Entity\Area;
use Cis\EducationalVisitBundle\Entity\Expense;
use Cis\EducationalVisitBundle\Entity\Income;
use Cis\EducationalVisitBundle\Entity\Itinerary;
use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Repository\VisitCriteria;
use Cis\EducationalVisitBundle\Tests\Fixture\AreaApproverTableFixture;
use Cis\EducationalVisitBundle\Tests\Fixture\AreaTableFixture;
use Cis\EducationalVisitBundle\Tests\Fixture\VisitTableFixture;

class VisitRepositoryTest extends EntityRepositoryTestCase
{
    protected function getTableFixtures()
    {
        return [
            new VisitTableFixture(),
            new AreaTableFixture(),
            new AreaApproverTableFixture(),
            new EmployeeTableFixture(),
            new UserTableFixture(),
            new EmptyEntityTableFixture(Expense::class),
            new EmptyEntityTableFixture(Income::class),
            new EmptyEntityTableFixture(Itinerary::class),
            new EmptyEntityTableFixture(StudentParticipant::class),
            new EmptyEntityTableFixture(StaffParticipant::class)
        ];
    }

    private function getRepository()
    {
        return $this->em->getRepository(Visit::class);
    }

    private function createArea(int $id)
    {
        $area = $this->prophesize(Area::class);
        $area->getId()->willReturn($id);
        return $area->reveal();
    }


    private function createEmployee(int $id)
    {
        $employee = $this->prophesize(Employee::class);
        $employee->getId()->willReturn($id);
        return $employee->reveal();
    }

    private function createUser(int $id)
    {
        $user = $this->prophesize(User::class);
        $user->getId()->willReturn($id);
        return $user->reveal();
    }

    /**
     * @dataProvider getMatchData
     */
    public function testMatch($numResults, $criteria)
    {
        $this->assertCount($numResults, $this->getRepository()->match($criteria));
    }

    public function getMatchData()
    {
        $criteria = new VisitCriteria();
        $criteria->academicYear = 2020;
        $criteria->orderBy = 'Date';
        yield [4, $criteria];

        $criteria = new VisitCriteria();
        $criteria->academicYear = 2019;
        $criteria->orderBy = 'EV Number';
        yield [2, $criteria];

        $criteria = new VisitCriteria();
        $criteria->academicYear = 2020;
        $criteria->keyword = 'Dartmoor';
        $criteria->orderBy = 'Educational Visit';
        yield [1, $criteria];

        $criteria = new VisitCriteria();
        $criteria->academicYear = 2020;
        $criteria->category = VisitTableFixture::DAY_TRIP_LOW_RISK_CATEGORY;
        $criteria->orderBy = 'Organiser';
        yield [2, $criteria];

        $criteria = new VisitCriteria();
        $criteria->academicYear = 2020;
        $criteria->area = $this->createArea(AreaTableFixture::STUDENT_SUPPORT_ID);
        yield [2, $criteria];

        $criteria = new VisitCriteria();
        $criteria->academicYear = 2020;
        $criteria->organiser = $this->createEmployee(EmployeeTableFixture::TED_SMITH_ID);
        yield [2, $criteria];
    }

    public function testFindUpcomingByEmployeeAndAreas()
    {
        $employee = $this->createEmployee(EmployeeTableFixture::GILL_COLLINS_ID);
        $area = $this->createArea(AreaTableFixture::STUDENT_SUPPORT_ID);
        $this->assertCount(1, $this->getRepository()->findUpcomingByEmployeeAndAreas($employee, [$area]));
    }

    public function testFindIssuesByEmployeeAndAreas()
    {
        $employee = $this->createEmployee(EmployeeTableFixture::GILL_COLLINS_ID);
        $area = $this->createArea(AreaTableFixture::STUDENT_SUPPORT_ID);
        $this->assertCount(2, $this->getRepository()->findIssuesByEmployeeAndAreas($employee, [$area]));
    }


    public function testFindPendingApprovalVisitByApprover()
    {
        $user = $this->createUser(UserTableFixture::TED_SMITH_ID);
        $this->assertCount(1, $this->getRepository()->findPendingApprovalVisitByApprover($user));
    }
}