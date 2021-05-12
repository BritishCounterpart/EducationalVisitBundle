<?php

namespace Cis\EducationalVisitBundle\View;

use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;

class PaymentList extends AbstractFilterableStudentParticipantList
{
    private $studentParticipant;

    public function __construct(ParticipantUtil $util, Visit $visit, StudentParticipant $studentParticipant = null)
    {
        $this->studentParticipant = $studentParticipant;
        parent::__construct($util, $visit);
    }

}