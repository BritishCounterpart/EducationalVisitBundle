<?php

namespace Cis\EducationalVisitBundle\Controller;

use Cis\EducationalVisitBundle\CommandBus\Detail\CancelCommand;
use Cis\EducationalVisitBundle\CommandBus\Detail\CompleteCommand;
use Cis\EducationalVisitBundle\CommandBus\Detail\EditCommand;
use Cis\EducationalVisitBundle\CommandBus\Detail\EditFinanceCodesCommand;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Form\Detail\EditFinanceCodesFormType;
use Cis\EducationalVisitBundle\Form\Detail\EditFormType;
use Petroc\Component\CommandBus\DeleteEntityCommand;

class DetailController extends Controller
{
    public function indexAction(Visit $visit)
    {
        return $this
            ->createView()
            ->setData($visit, 'visit')
	        ->restrictTo(self::ACCESS_RULE)
	    ;
    }
    
    public function addAction()
    {
        $command = new EditCommand();
        return $this
            ->createFormView(EditFormType::class, $command)
            ->onSuccessRoute(
                self::ROUTE_EXPENSES_AND_INCOME_EDIT,
                $this->createPropertyCallback($command, 'visit')
            )
	        ->restrictTo(self::ACCESS_RULE)
        ;
    }

    public function editAction(Visit $visit)
    {
        return $this
            ->createFormView(EditFormType::class, new EditCommand($visit))
            ->onSuccessRoute(self::ROUTE_DETAIL, $visit)
            ->setData($visit,'visit')
            ->restrictTo(self::ACCESS_RULE_EDIT, $visit)
        ;
    }

    public function financeCodesAction(Visit $visit)
    {
        return $this
            ->createFormView(EditFinanceCodesFormType::class, new EditFinanceCodesCommand($visit))
            ->onSuccessRoute(self::ROUTE_DETAIL, $visit)
            ->setData($visit,'visit')
            ->restrictTo(self::ACCESS_RULE_FINANCE)
        ;
    }

    public function deleteAction(Visit $visit)
    {
        return $this
            ->createConfirmationView(new DeleteEntityCommand($visit))
            ->onSuccessRoute(self::ROUTE_INDEX)
            ->onSuccessMessage('Visit Deleted.')
            ->setData($visit,'visit')
            ->restrictTo(self::ACCESS_RULE_EDIT, $visit)
        ;
    }

    public function cancelAction(Visit $visit)
    {
        return $this
            ->createCommandView(new CancelCommand($visit))
            ->onSuccessMessage('Visit cancelled.')
            ->onSuccessRoute(self::ROUTE_DETAIL, $visit)
            ->restrictTo(self::ACCESS_RULE_EDIT, $visit)
        ;
    }


    public function completeAction(Visit $visit)
    {
        return $this
            ->createCommandView(new CompleteCommand($visit))
            ->onSuccessMessage('Visit completed.')
            ->onSuccessRoute(self::ROUTE_DETAIL, $visit)
            ->restrictTo(self::ACCESS_RULE_EDIT, $visit)
        ;
    }
}
