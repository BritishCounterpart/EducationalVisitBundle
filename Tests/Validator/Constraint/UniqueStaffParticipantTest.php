<?php

namespace Cis\EducationalVisitBundle\Tests\Validator\Constraint;

use Cis\EducationalVisitBundle\Validator\Constraint\UniqueStaffParticipant;
use Petroc\Bridge\PhpUnit\TestCase;
use Symfony\Component\Validator\Constraint;

class UniqueStaffParticipantTest extends TestCase
{
    public function testConstructor()
    {
        $constraint = new UniqueStaffParticipant();
        $this->assertSame('Staff member already added.', $constraint->message);
    }

    public function testGetTargets()
    {
        $constraint = new UniqueStaffParticipant();
        $this->assertSame($constraint->getTargets(), Constraint::CLASS_CONSTRAINT);
    }
}
