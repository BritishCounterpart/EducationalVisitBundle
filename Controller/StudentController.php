<?php

namespace Cis\EducationalVisitBundle\Controller;

use App\Entity\Student\Student;
use Cis\EducationalVisitBundle\CommandBus\Participant\AddVisitConsentCommand;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Cis\EducationalVisitBundle\View\StudentVisitList;

class StudentController extends Controller
{
    public function indexAction(Student $student, ParticipantUtil $util)
    {
        $list = new StudentVisitList($util, $student);
        return $this
            ->createView()
            ->setData($list, 'list')
            ->restrictTo(self::ACCESS_RULE_STUDENT, $student)
        ;
    }

    public function addVisitConsentAction(StudentParticipant $studentParticipant)
    {
        $visit = $studentParticipant->getVisit();
        $student = $studentParticipant->getStudent();
        return $this
            ->createConfirmationView(new AddVisitConsentCommand($studentParticipant))
            ->setData($visit, 'visit')
            ->setTemplateData(['student' => $student])
            ->onSuccessMessage('Visit consent added.')
            ->onSuccessRoute(self::ROUTE_STUDENT, $student)
            ->restrictTo(self::ACCESS_RULE_STUDENT_PARTICIPANT_VISIT_CONSENT, $student)
        ;
    }
}