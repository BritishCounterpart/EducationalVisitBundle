<?php

namespace Cis\EducationalVisitBundle\Controller;

use Cis\EducationalVisitBundle\CommandBus\Payment\ChangeStudentParticipantPaymentAmountCommand;
use Cis\EducationalVisitBundle\CommandBus\Payment\TransferPaymentToStudentParticipantCommand;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Form\Payment\ChangeStudentParticipantPaymentAmountFormType;
use Cis\EducationalVisitBundle\Form\Payment\FilterFormType;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Cis\EducationalVisitBundle\View\PaymentList;
use Cis\EducationalVisitBundle\View\TransferPaymentList;

class PaymentController extends Controller
{
    public function indexAction(Visit $visit, ParticipantUtil $util)
    {
        $list = new PaymentList($util, $visit);
        return $this
            ->createFormView(FilterFormType::class, $list)
            ->setData($list, 'list')
            ->setTemplateData(['visit' => $visit])
            ->restrictTo(self::ACCESS_RULE)
        ;
    }

    public function changeStudentParticipantPaymentAmountAction(StudentParticipant $studentParticipant)
    {
        $visit = $studentParticipant->getVisit();
        return $this
            ->createFormView(
                ChangeStudentParticipantPaymentAmountFormType::class,
                new ChangeStudentParticipantPaymentAmountCommand($studentParticipant)
            )
            ->setData($studentParticipant, 'studentParticipant')
            ->setTemplateData(['visit' => $visit])
            ->restrictTo(self::ACCESS_RULE_EDIT, $visit)
            ->onSuccessRoute(self::ROUTE_PAYMENTS, $visit)
        ;
    }

    public function transferStudentParticipantPaymentAction(StudentParticipant $studentParticipant, ParticipantUtil $util)
    {
        $visit = $studentParticipant->getVisit();
        $list = new TransferPaymentList($util, $studentParticipant);
        return $this
            ->createView()
            ->setData($list, 'list')
            ->setTemplateData([
                'visit' => $visit,
                'studentParticipant' => $studentParticipant
            ])
            ->restrictTo(self::ACCESS_RULE_EDIT, $visit)
        ;
    }

    public function transferPaymentToStudentParticipantAction(StudentParticipant $fromStudentParticipant, StudentParticipant $toStudentParticipant)
    {
        $visit = $fromStudentParticipant->getVisit();
        $command = new TransferPaymentToStudentParticipantCommand($fromStudentParticipant, $toStudentParticipant);
        return $this
            ->createCommandView($command)
            ->restrictTo(self::ACCESS_RULE_EDIT, $visit)
            ->onSuccessMessage('Transfer successful.')
            ->onSuccessRoute(self::ROUTE_PAYMENTS, $visit)
        ;
    }
}
