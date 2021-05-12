<?php

namespace Cis\EducationalVisitBundle\Tests\Fixture;

use App\Tests\EntityTableFixtureInterface;
use App\Tests\Fixture\Employee\EmployeeTableFixture;
use Cis\EducationalVisitBundle\Entity\Visit;
use DateTime;
use DateInterval;
use Traversable;

class VisitTableFixture implements EntityTableFixtureInterface
{
    const DARTMOOR_TRIP_ID = 1;
    const LONDON_TRIP_ID = 2;
    const FRANCE_TRIP_ID = 3;
    const SPAIN_TRIP_ID = 4;
    const BIDEFORD_TRIP_ID = 5;
    const SWEDEN_TRIP_ID = 6;
    const DAY_TRIP_LOW_RISK_CATEGORY = 'DTLR';
    const OVERSEAS_TRIP_HIGH_RISK_CATEGORY = 'OHR';

    public function getName(): string
    {
        return 'PETROC_EDUCATIONAL_VISIT';
    }

    public function getEntityClass(): string
    {
        return Visit::class;
    }

    public function getRows(): Traversable
    {
        $startDate = new DateTime;
        yield [
            'object_id' => self::DARTMOOR_TRIP_ID,
            'timestamp' => new DateTime,
            'academic_year' => 2020,
            'title' => 'Dartmoor Trip',
            'status' => Visit::STATUS_APPROVED,
            'start_date' => $startDate->format('Y-m-d'),
            'show_on_calendar' => 1,
            'has_expenses' => 1,
            'osa_required' => 1,
            'visit_full' => 0,
            'organiser' => EmployeeTableFixture::TED_SMITH_ID,
            'petroc_work_area' => AreaTableFixture::HEALTH_CARE_ID,
            'category' => self::DAY_TRIP_LOW_RISK_CATEGORY
        ];

        $startDate = new DateTime;
        yield [
            'object_id' => self::LONDON_TRIP_ID,
            'timestamp' => new DateTime,
            'academic_year' => 2020,
            'title' => 'London Trip',
            'status' => Visit::STATUS_APPROVED,
            'start_date' => $startDate->format('Y-m-d'),
            'show_on_calendar' => 1,
            'has_expenses' => 1,
            'osa_required' => 1,
            'visit_full' => 0,
            'organiser' => EmployeeTableFixture::TED_SMITH_ID,
            'petroc_work_area' => AreaTableFixture::HEALTH_CARE_ID,
            'category' => self::DAY_TRIP_LOW_RISK_CATEGORY
        ];

        $startDate = new DateTime;
        yield [
            'object_id' => self::FRANCE_TRIP_ID,
            'timestamp' => new DateTime,
            'academic_year' => 2020,
            'title' => 'France Trip',
            'status' => Visit::STATUS_PLANNED,
            'start_date' => $startDate->format('Y-m-d'),
            'show_on_calendar' => 1,
            'has_expenses' => 1,
            'osa_required' => 1,
            'visit_full' => 0,
            'organiser' => EmployeeTableFixture::GILL_COLLINS_ID,
            'petroc_work_area' => AreaTableFixture::STUDENT_SUPPORT_ID,
            'category' => self::OVERSEAS_TRIP_HIGH_RISK_CATEGORY,
            'issues' => 'Pending Expenses'
        ];


        $startDate = new DateTime;
        $startDate->add(new DateInterval('P100D'));
        yield [
            'object_id' => self::SPAIN_TRIP_ID,
            'timestamp' => new DateTime,
            'academic_year' => 2020,
            'title' => 'Spain Trip',
            'status' => Visit::STATUS_PLANNED,
            'start_date' => $startDate->format('Y-m-d'),
            'show_on_calendar' => 1,
            'has_expenses' => 1,
            'osa_required' => 1,
            'visit_full' => 0,
            'organiser' => EmployeeTableFixture::GILL_COLLINS_ID,
            'petroc_work_area' => AreaTableFixture::STUDENT_SUPPORT_ID,
            'category' => self::OVERSEAS_TRIP_HIGH_RISK_CATEGORY,
            'issues' => 'Pending Risk Assessment'
        ];

        $startDate = new DateTime;
        $startDate->sub(new DateInterval('P1Y'));
        yield [
            'object_id' => self::BIDEFORD_TRIP_ID,
            'timestamp' => new DateTime,
            'academic_year' => 2019,
            'title' => 'France Trip',
            'status' => Visit::STATUS_PLANNED,
            'start_date' => $startDate->format('Y-m-d'),
            'show_on_calendar' => 1,
            'has_expenses' => 1,
            'osa_required' => 1,
            'visit_full' => 0,
            'organiser' => EmployeeTableFixture::GILL_COLLINS_ID,
            'petroc_work_area' => AreaTableFixture::STUDENT_SUPPORT_ID,
            'category' => self::DAY_TRIP_LOW_RISK_CATEGORY
        ];

        $startDate = new DateTime;
        $startDate->sub(new DateInterval('P1Y'));
        yield [
            'object_id' => self::SWEDEN_TRIP_ID,
            'timestamp' => new DateTime,
            'academic_year' => 2019,
            'title' => 'Sweden Trip',
            'status' => Visit::STATUS_PENDING_APPROVAL,
            'start_date' => $startDate->format('Y-m-d'),
            'show_on_calendar' => 1,
            'has_expenses' => 1,
            'osa_required' => 1,
            'visit_full' => 0,
            'organiser' => EmployeeTableFixture::TED_SMITH_ID,
            'petroc_work_area' => AreaTableFixture::STUDENT_SUPPORT_ID,
            'category' => self::OVERSEAS_TRIP_HIGH_RISK_CATEGORY
        ];
    }
}