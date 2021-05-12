<?php

namespace Cis\EducationalVisitBundle\Tests\Repository;

use Cis\EducationalVisitBundle\Repository\VisitCriteria;
use Petroc\Bridge\PhpUnit\TestCase;

class VisitCriteriaTest extends TestCase
{
    public function testConstructor()
    {
        $criteria = new VisitCriteria();
        $this->assertNull($criteria->academicYear);
        $this->assertNull($criteria->keyword);
        $this->assertNull($criteria->category);
        $this->assertNull($criteria->area);
        $this->assertNull($criteria->organiser);
        $this->assertNull($criteria->orderBy);
    }
}
