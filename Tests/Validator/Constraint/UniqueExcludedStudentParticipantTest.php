<?php

namespace Cis\EducationalVisitBundle\Tests\Validator\Constraint;

use Cis\EducationalVisitBundle\Validator\Constraint\UniqueExcludedStudentParticipant;
use Petroc\Bridge\PhpUnit\TestCase;
use Symfony\Component\Validator\Constraint;

class UniqueExcludedStudentParticipantTest extends TestCase
{
    public function testConstructor()
    {
        $constraint = new UniqueExcludedStudentParticipant();
        $this->assertSame('Student already removed.', $constraint->message);
    }

    public function testGetTargets()
    {
        $constraint = new UniqueExcludedStudentParticipant();
        $this->assertSame($constraint->getTargets(), Constraint::CLASS_CONSTRAINT);
    }
}
