<?php

namespace Cis\EducationalVisitBundle\Controller;

use Cis\EducationalVisitBundle\CommandBus\Participant\AddStaffParticipantCommand;
use Cis\EducationalVisitBundle\CommandBus\Participant\AddVisitConsentCommand;
use Cis\EducationalVisitBundle\CommandBus\Participant\RefreshStudentsCommand;
use Cis\EducationalVisitBundle\CommandBus\Participant\ExcludeStudentCommand;
use Cis\EducationalVisitBundle\CommandBus\Participant\UnexcludeStudentCommand;
use Cis\EducationalVisitBundle\Entity\ExcludedStudentParticipant;
use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Form\Participant\AddStaffParticipantFormType;
use Cis\EducationalVisitBundle\Form\Participant\FilterFormType;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Cis\EducationalVisitBundle\View\AbstractFilterableStudentParticipantList;
use Cis\EducationalVisitBundle\View\NoPortalAccessList;
use Cis\EducationalVisitBundle\View\ParticipantList;
use Petroc\Component\CommandBus\DeleteEntityCommand;

class ParticipantController extends Controller
{
    public function indexAction(Visit $visit, ParticipantUtil $util)
    {
        $list = new ParticipantList($util, $visit);
        return $this
            ->createFormView(FilterFormType::class, $list)
            ->setData($list, 'list')
            ->setTemplateData(['visit' => $visit])
            ->restrictTo(self::ACCESS_RULE)
        ;
    }
    
    public function refreshStudentsAction(Visit $visit)
    {
        return $this
            ->createCommandView(new RefreshStudentsCommand($visit))
            ->onSuccessRoute(self::ROUTE_PARTICIPANTS, $visit)
            ->restrictTo(self::ACCESS_RULE)
        ;
    }

    public function excludeStudentAction(StudentParticipant $studentParticipant)
    {
        $visit = $studentParticipant->getVisit();
        return $this
            ->createCommandView(new ExcludeStudentCommand($studentParticipant))
            ->onSuccessMessage('Student removed.')
            ->onFailureMessage('Student already removed.')
            ->onFailureRoute(self::ROUTE_PARTICIPANTS, $visit)
            ->onSuccessRoute(self::ROUTE_PARTICIPANTS, $visit)
            ->restrictTo(self::ACCESS_RULE_EDIT, $visit)
        ;
    }

    public function unexcludeStudentAction(ExcludedStudentParticipant $excludedStudentParticipant)
    {
        $visit = $excludedStudentParticipant->getVisit();
        return $this
            ->createCommandView(new UnexcludeStudentCommand($excludedStudentParticipant))
            ->onSuccessMessage('Student unexcluded.')
            ->onSuccessRoute(self::ROUTE_PARTICIPANTS, $visit)
            ->restrictTo(self::ACCESS_RULE_EDIT, $visit)
        ;
    }

    public function noPortalAccessAction(Visit $visit)
    {
        return $this
            ->createView()
            ->setData(new NoPortalAccessList($visit), 'students')
            ->setTemplateData(['visit' => $visit])
            ->restrictTo(self::ACCESS_RULE)
        ;
    }

    public function marketingConsentAction(Visit $visit, ParticipantUtil $util)
    {
        return $this
            ->createView()
            ->setData(new ParticipantList($util, $visit), 'list')
            ->setTemplateData(['visit' => $visit])
            ->restrictTo(self::ACCESS_RULE)
        ;
    }

    public function osa7ContactListAction(Visit $visit, ParticipantUtil $util)
    {
        $list = new ParticipantList($util, $visit);
        $list->paymentStatus = AbstractFilterableStudentParticipantList::PAYMENT_STATUS_PAID;
        $list->showNoLongerGoing = false;

        return $this
            ->createView()
            ->setData($list, 'list')
            ->setTemplateData(['visit' => $visit])
            ->restrictTo(self::ACCESS_RULE)
        ;
    }

    public function addVisitConsentAction(StudentParticipant $studentParticipant)
    {
        $visit = $studentParticipant->getVisit();
        return $this
            ->createCommandView(new AddVisitConsentCommand($studentParticipant))
            ->onSuccessMessage('Visit consent added.')
            ->onSuccessRoute(self::ROUTE_PARTICIPANTS, $visit)
            ->restrictTo(self::ACCESS_RULE_EDIT, $visit)
        ;
    }

    public function addStaffParticipantAction(Visit $visit)
    {
        return $this
            ->createFormView(AddStaffParticipantFormType::class, new AddStaffParticipantCommand($visit))
            ->setData($visit, 'visit')
            ->onSuccessRoute(self::ROUTE_PARTICIPANTS, $visit)
            ->restrictTo(self::ACCESS_RULE_EDIT, $visit)
        ;
    }

    public function deleteStaffParticipantAction(StaffParticipant $staffParticipant)
    {
        $visit = $staffParticipant->getVisit();
        return $this
            ->createCommandView(new DeleteEntityCommand($staffParticipant))
            ->onSuccessMessage('Staff member removed.')
            ->onSuccessRoute(self::ROUTE_PARTICIPANTS, $visit)
            ->restrictTo(self::ACCESS_RULE_EDIT, $visit)
        ;
    }
}
