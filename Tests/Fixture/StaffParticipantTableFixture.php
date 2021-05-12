<?php

namespace Cis\EducationalVisitBundle\Tests\Fixture;

use App\Tests\EntityTableFixtureInterface;
use App\Tests\Fixture\Employee\EmployeeTableFixture;
use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Traversable;
use DateTime;

class StaffParticipantTableFixture implements EntityTableFixtureInterface
{
    const DARTMOOR_TRIP_TED_SMITH_ID = 1;
    const SPAIN_TRIP_TED_SMITH_ID = 2;
    const DARTMOOR_TRIP_GILL_COLLINS_ID = 3;

    public function getName(): string
    {
        return 'PETROC_EDUCATIONAL_VISIT_STAFF_PARTICIPANT';
    }

    public function getEntityClass(): string
    {
        return StaffParticipant::class;
    }

    public function getRows(): Traversable
    {
        yield [
            'object_id' => self::DARTMOOR_TRIP_TED_SMITH_ID,
            'timestamp' => new DateTime,
            'petroc_educational_visit' => VisitTableFixture::DARTMOOR_TRIP_ID,
            'employee' => EmployeeTableFixture::TED_SMITH_ID
        ];

        yield [
            'object_id' => self::SPAIN_TRIP_TED_SMITH_ID,
            'timestamp' => new DateTime,
            'petroc_educational_visit' => VisitTableFixture::SPAIN_TRIP_ID,
            'employee' => EmployeeTableFixture::TED_SMITH_ID
        ];

        yield [
            'object_id' => self::DARTMOOR_TRIP_GILL_COLLINS_ID,
            'timestamp' => new DateTime,
            'petroc_educational_visit' => VisitTableFixture::DARTMOOR_TRIP_ID,
            'employee' => EmployeeTableFixture::GILL_COLLINS_ID
        ];
    }
}