<?php

namespace Cis\EducationalVisitBundle\Tests\Fixture;

use App\Tests\EntityTableFixtureInterface;
use App\Tests\Fixture\Student\StudentTableFixture;
use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Traversable;
use Datetime;

class ExcludedStudentParticipantTableFixture implements EntityTableFixtureInterface
{
    const DARTMOOR_TRIP_JOHN_BARNES_ID = 1;
    const DARTMOOR_TRIP_CHRIS_WITTY_ID = 2;
    const SPAIN_TRIP_WENDY_SMITH_ID = 3;

    public function getName(): string
    {
        return 'PETROC_EDUCATIONAL_VISIT_EXCLUDED_STUDENT_PARTICIPANT';
    }

    public function getEntityClass(): string
    {
        return ExcludedStudentParticipant::class;
    }

    public function getRows(): Traversable
    {
        yield [
            'object_id' => self::DARTMOOR_TRIP_JOHN_BARNES_ID,
            'timestamp' => new DateTime,
            'petroc_educational_visit' => VisitTableFixture::DARTMOOR_TRIP_ID,
            'student' => StudentTableFixture::JOHN_BARNES_ID
        ];

        yield [
            'object_id' => self::DARTMOOR_TRIP_CHRIS_WITTY_ID,
            'timestamp' => new DateTime,
            'petroc_educational_visit' => VisitTableFixture::DARTMOOR_TRIP_ID,
            'student' => StudentTableFixture::CHRIS_WITTY_ID
        ];

        yield [
            'object_id' => self::SPAIN_TRIP_WENDY_SMITH_ID,
            'timestamp' => new DateTime,
            'petroc_educational_visit' => VisitTableFixture::SPAIN_TRIP_ID,
            'student' => StudentTableFixture::WENDY_SMITH_ID
        ];
    }
}