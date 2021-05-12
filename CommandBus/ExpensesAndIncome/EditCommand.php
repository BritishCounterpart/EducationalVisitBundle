<?php

namespace Cis\EducationalVisitBundle\CommandBus\ExpensesAndIncome;

use Cis\EducationalVisitBundle\Entity\Expense;
use Cis\EducationalVisitBundle\Entity\Income;
use Cis\EducationalVisitBundle\Entity\Visit;
use Symfony\Component\Validator\GroupSequenceProviderInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Petroc\Component\CommandBus\Command;
use Symfony\Component\Validator\Constraints as Assert;

class EditCommand extends Command implements GroupSequenceProviderInterface
{
    const DEFAULT_GROUP = 'default';
    const HAS_EXPENSES_GROUP = 'has_expenses';
    const HAS_INCOME_GROUP = 'has_income';

    private $visit;
    // Cost Section
    public $anyExpenses = true;
    public $expenses = [];
    // Income Section
    public $anyIncome = false;
    public $studentsPays = 0.00;
    public $collegePays = 0.00;
    public $otherPayInfo;
    public $otherPay = 0.00;

    public function __construct(Visit $visit)
    {
        $this->visit = $visit;
        $this->anyExpenses = $visit->hasExpenses();

        foreach ($visit->getExpenses() as $expense) {
            $this->expenses[$expense->getId()] = [
                'type' => $expense->getType(),
                'description' => $expense->getDescription(),
                'amount' => $expense->getAmount(),
            ];
        }

        if(null !== $income = $visit->getIncome()) {
            $this->anyIncome = $visit->hasIncome();
            $this->studentsPays = $income->getIncomeStudent();
            $this->collegePays = $income->getIncomeCollege();
            $this->otherPayInfo = $income->getIncomeOtherFrom();
            $this->otherPay = $income->getIncomeOther();
        }
    }

    public function getVisit()
    {
        return $this->visit;
    }

    public function getGroupSequence()
    {
        $groups = [self::DEFAULT_GROUP];

        if($this->anyExpenses === true) {
            $groups[] = self::HAS_EXPENSES_GROUP;
        }

        if($this->anyIncome === true) {
            $groups[] = self::HAS_INCOME_GROUP;
        }

        return $groups;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->setGroupSequenceProvider(true);

        $metadata->addPropertyConstraint('otherPayInfo', new Assert\Length([
            'groups' => self::DEFAULT_GROUP,
            'max' => Income::MAX_LENGTH_INCOME_OTHER_FROM
        ]));
        $metadata->addPropertyConstraint('expenses', new Assert\All([
            'constraints' => new Assert\Collection([
                'groups' => self::HAS_EXPENSES_GROUP,
                'fields' => [
                    'type' => new Assert\NotNull(['groups' => self::HAS_EXPENSES_GROUP]),
                    'description' => [
                        new Assert\NotBlank(['groups' => self::HAS_EXPENSES_GROUP]),
                        new Assert\Length([
                            'groups' => self::HAS_EXPENSES_GROUP,
                            'max' => Expense::MAX_LENGTH_DESCRIPTION
                        ]),
                    ],
                    'amount' => new Assert\NotNull(['groups' => self::HAS_EXPENSES_GROUP]),
                ]
            ])
        ]));

        $metadata->addPropertyConstraint('studentsPays', new Assert\NotBlank(['groups' => self::HAS_INCOME_GROUP]));
        $metadata->addPropertyConstraint('collegePays', new Assert\NotBlank(['groups' => self::HAS_INCOME_GROUP]));
        $metadata->addPropertyConstraint('otherPay', new Assert\NotBlank(['groups' => self::HAS_INCOME_GROUP]));

    }
}