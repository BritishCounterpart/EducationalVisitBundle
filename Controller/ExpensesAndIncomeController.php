<?php

namespace Cis\EducationalVisitBundle\Controller;

use Cis\EducationalVisitBundle\CommandBus\ExpensesAndIncome\EditCommand;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Form\ExpensesAndIncome\EditFormType;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Cis\EducationalVisitBundle\View\ExpensesAndIncomeList;

class ExpensesAndIncomeController extends Controller
{
    public function indexAction(Visit $visit, ParticipantUtil $util)
    {
        return $this
            ->createView()
            ->restrictTo(self::ACCESS_RULE)
            ->setData(new ExpensesAndIncomeList($util, $visit), 'list')
            ->setTemplateData(['visit' => $visit])
        ;
    }

    public function editAction(Visit $visit)
    {
        return $this
            ->createFormView(EditFormType::class, new EditCommand($visit))
            ->onSuccessRoute(self::ROUTE_EXPENSES_AND_INCOME, $visit)
            ->restrictTo(self::ACCESS_RULE_EDIT, $visit)
            ->setData($visit, 'visit')
        ;
    }
}
