<?php

namespace Cis\EducationalVisitBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class UniqueStaffParticipant extends Constraint
{
    public $message = 'Staff member already added.';

    public function getTargets()
    {
        return parent::CLASS_CONSTRAINT;
    }
}
