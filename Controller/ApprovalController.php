<?php

namespace Cis\EducationalVisitBundle\Controller;

use Cis\EducationalVisitBundle\CommandBus\Approval\ApproveCommand;
use Cis\EducationalVisitBundle\CommandBus\Approval\RejectCommand;
use Cis\EducationalVisitBundle\CommandBus\Approval\RequestCommand;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Form\Approval\RejectFormType;
use Symfony\Component\Security\Core\User\UserInterface;

class ApprovalController extends Controller
{
    public function requestAction(Visit $visit)
    {
        return $this
            ->createCommandView(new RequestCommand($visit))
            ->restrictTo(self::ACCESS_RULE_APPROVAL_REQUEST, $visit)
            ->onSuccessMessage('Approval request sent.')
            ->onSuccessRoute(self::ROUTE_DETAIL, $visit)
        ;
    }

    public function approveAction(Visit $visit, UserInterface $user)
    {
        return $this
            ->createCommandView(new ApproveCommand($visit, $user))
            ->restrictTo(self::ACCESS_RULE_APPROVAL_CAN_APPROVE, $visit)
            ->onSuccessMessage('Visit approved.')
            ->onSuccessRoute(self::ROUTE_REFRESH_ALERTS)
        ;
    }

    public function rejectAction(Visit $visit, UserInterface $user)
    {
        return $this
            ->createFormView(RejectFormType::class, new RejectCommand($visit, $user))
            ->setData($visit, 'visit')
            ->restrictTo(self::ACCESS_RULE_APPROVAL_CAN_APPROVE, $visit)
            ->onSuccessMessage('Visit rejected.')
            ->onSuccessRoute(self::ROUTE_REFRESH_ALERTS)
        ;
    }
}