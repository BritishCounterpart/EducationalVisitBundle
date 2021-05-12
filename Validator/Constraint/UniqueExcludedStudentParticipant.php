<?php

namespace Cis\EducationalVisitBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class UniqueExcludedStudentParticipant extends Constraint
{
    public $message = 'Student already removed.';

    public function getTargets()
    {
        return parent::CLASS_CONSTRAINT;
    }
}
