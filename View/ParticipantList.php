<?php

namespace Cis\EducationalVisitBundle\View;

use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Petroc\Component\View\OrmData;

class ParticipantList extends AbstractFilterableStudentParticipantList
{
    private $staffParticipants;
    private $excludedStudentParticipant;

    public function __construct(ParticipantUtil $util, Visit $visit)
    {
        parent::__construct($util, $visit);
    }

    public function getStaffParticipants()
    {
        if(null !== $staffParticipants = $this->staffParticipants) {
            return $staffParticipants;
        }

        $staffParticipants = $this->getRepository(StaffParticipant::class)->findByVisit($this->visit);

        return $this->staffParticipants = $staffParticipants;
    }

    public function getExcludedStudentParticipants()
    {
        if(null !== $excludedStudentParticipant = $this->excludedStudentParticipant) {
            return $excludedStudentParticipant;
        }

        $excludedStudentParticipant = $this->getRepository(ExcludedStudentParticipant::class)->findByVisit($this->visit);

        return $this->excludedStudentParticipant = $excludedStudentParticipant;
    }

}