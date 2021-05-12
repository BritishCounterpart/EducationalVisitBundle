<?php

namespace Cis\EducationalVisitBundle\Tests\Fixture;

use App\Tests\EntityTableFixtureInterface;
use Cis\EducationalVisitBundle\Entity\Area;
use Traversable;

class AreaTableFixture implements EntityTableFixtureInterface
{
    const STUDENT_SUPPORT_ID = 1;
    const HEALTH_CARE_ID = 2;

    public function getName(): string
    {
        return 'PETROC_V_EDUCATIONAL_VISIT_AREA';
    }

    public function getEntityClass(): string
    {
        return Area::class;
    }

    public function getRows(): Traversable
    {
        yield [
            'object_id' => self::STUDENT_SUPPORT_ID,
            'name' => 'Student Support'
        ];

        yield [
            'object_id' => self::HEALTH_CARE_ID,
            'name' => 'Health Care'
        ];
    }
}