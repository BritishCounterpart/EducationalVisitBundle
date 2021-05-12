<?php

namespace Cis\EducationalVisitBundle\Tests\Fixture;

use App\Tests\EntityTableFixtureInterface;
use App\Tests\Fixture\Employee\EmployeeTableFixture;
use App\Tests\Fixture\User\UserTableFixture;
use Cis\EducationalVisitBundle\Entity\AreaApprover;
use Traversable;

class AreaApproverTableFixture implements EntityTableFixtureInterface
{
    const STUDENT_SUPPORT_APPROVER_ID = 1;

    public function getName(): string
    {
        return 'PETROC_V_EDUCATIONAL_VISIT_AREA_APPROVER';
    }

    public function getEntityClass(): string
    {
        return AreaApprover::class;
    }

    public function getRows(): Traversable
    {
        yield [
            'object_id' => self::STUDENT_SUPPORT_APPROVER_ID,
            'petroc_work_area' => AreaTableFixture::STUDENT_SUPPORT_ID,
            'employee' => EmployeeTableFixture::TED_SMITH_ID,
            'petroc_user' => UserTableFixture::TED_SMITH_ID
        ];
    }
}