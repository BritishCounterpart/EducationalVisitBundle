<?php

namespace Cis\EducationalVisitBundle\CommandBus\ExpensesAndIncome;

use Cis\EducationalVisitBundle\Entity\Expense;
use Cis\EducationalVisitBundle\Entity\Income;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Messenger\EducationalVisitMessenger;
use Petroc\Component\CommandBus\HandlerInterface;
use Petroc\Component\Helper\Orm;

class EditHandler implements HandlerInterface
{
    private $orm;
    private $messenger;

    public function __construct(Orm $orm, EducationalVisitMessenger $messenger)
    {
        $this->orm = $orm;
        $this->messenger = $messenger;
    }

    public function handle(EditCommand $command)
    {
        $orm = $this->orm;
        $visit = $command->getVisit();
        $originalExpenses = clone $visit->getExpenses();

        if(false === $anyExpenses = $command->anyExpenses) {
            $visit->setHasExpenses($anyExpenses);
        } else {
            $visit->setHasExpenses($anyExpenses);
            $origExpenses = $visit->getExpenses()->toArray();
            foreach ($command->expenses as $id => $data) {
                $found = false;
                $type = $data['type'];
                $description = $data['description'];
                $amount = $data['amount'];

                // Update existing expenses
                foreach ($origExpenses as $expense) {
                    if ($id != $expense->getId()) {
                        continue;
                    }

                    $expense->setType($type);
                    $expense->setDescription($description);
                    $expense->setAmount($amount);
                    $found = true;
                    continue;
                }

                if ($found) {
                    continue;
                }

                // Add new expenses
                $expense = new Expense(
                    $visit,
                    $type,
                    $description,
                    $amount
                );
                $orm->persist($expense);

            }

            // Remove expenses that no longer exist
            foreach ($origExpenses as $expense) {
                $found = false;
                foreach ($command->expenses as $id => $data) {
                    if ($id == $expense->getId()) {
                        $found = true;
                        continue;
                    }
                }

                if ($found) {
                    continue;
                }

                $visit->removeExpense($expense);
            }
        }

        // Send email if Expenses have changed on an approved visit
        if($originalExpenses !== $visit->getExpenses() and $visit->getStatus() === Visit::STATUS_APPROVED) {
            $this->messenger->sendExpensesChangedOnApprovedVisit(
                $originalExpenses,
                $visit
            );
        }

        // If income exist update else add new
        if(null !== $income = $visit->getIncome()) {
            if($command->anyIncome === true) {
                $income->setIncomeStudent($command->studentsPays);
                $income->setIncomeCollege($command->collegePays);
                $income->setIncomeOtherFrom($command->otherPayInfo);
                $income->setIncomeOther($command->otherPay);
            } else {
                $income->setIncomeStudent(0);
                $income->setIncomeCollege(0);
                $income->setIncomeOtherFrom(null);
                $income->setIncomeOther(0);
            }
        } elseif($command->anyIncome === true) {
            $income = new Income($visit);
            $income->setIncomeStudent($command->studentsPays);
            $income->setIncomeCollege($command->collegePays);
            $income->setIncomeOtherFrom($command->otherPayInfo);
            $income->setIncomeOther($command->otherPay);
            $orm->persist($income);
        }
    }
}