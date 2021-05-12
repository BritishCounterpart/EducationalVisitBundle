<?php

namespace Cis\EducationalVisitBundle\CommandBus\Participant;

use App\Entity\Cohort\Cohort;
use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Petroc\Component\CommandBus\HandlerInterface;
use Petroc\Component\Helper\Orm;

class RefreshStudentsHandler implements HandlerInterface
{
    private $orm;
    private $util;

    public function __construct(Orm $orm, ParticipantUtil $util)
    {
        $this->orm = $orm;
        $this->util = $util;
    }

    public function handle(RefreshStudentsCommand $command)
    {
        $orm = $this->orm;
        $util = $this->util;
        $visit = $command->getVisit();

        $cohorts = $orm->getRepository(Cohort::class)->findByObjectAndReference(Visit::COHORT_OBJECT, $visit->getId());

        $studentParticipants = $orm->getRepository(StudentParticipant::class)->findByVisit($visit);

        // Exclude students who are within the excluded list or that are already attached to a visit
        $excludedStudents = $orm->getRepository(ExcludedStudentParticipant::class)->findStudentsByVisit($visit);

        foreach($studentParticipants as $studentParticipant) {
            $excludedStudents[] = $studentParticipant->getStudent();
        }

        $importedStudents = [];

        // Get all students meeting the criteria and add
        foreach($cohorts as $cohort) {
            $cohortStudents = $this->util->getCohortStudents($cohort);
            $importedStudents = array_merge($importedStudents, $cohortStudents);
            $util->addStudents($visit, $cohort, $cohortStudents, $excludedStudents);
        }

        // Only delete if they're no longer meeting the criteria and were imported via cohorts
        foreach($studentParticipants as $studentParticipant) {
            if(!in_array($studentParticipant->getStudent(), $importedStudents) and $studentParticipant->getCohort() !== null) {
                $studentPayment = $util->getStudentPayment($studentParticipant);
                $util->removeStudent($studentParticipant, $studentPayment);
            }
        }

    }
}