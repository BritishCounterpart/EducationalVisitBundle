<?php

namespace Cis\EducationalVisitBundle\View;

use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Cis\EducationalVisitBundle\Util\PaymentUtil;

class ExpensesAndIncomeList
{
    private $util;
    private $visit;
    private $expenses;
    private $income;
    private $studentParticipants;
    private $actualStudentIncome;

    public function __construct(ParticipantUtil $util, Visit $visit)
    {
        $this->util = $util;
        $this->visit = $visit;
        $this->expenses = $visit->getExpenses();
        $this->income = $visit->getIncome();
        $this->studentParticipants = $visit->getStudentParticipants();
    }

    public function getVisit()
    {
        return $this->visit;
    }

    public function getExpenses()
    {
        return $this->expenses;
    }

    public function getIncome()
    {
        return $this->income;
    }

    public function getStudentParticipants()
    {
        return $this->studentParticipants;
    }

    public function getActualStudentIncome()
    {
        if(null !== $actualStudentIncome = $this->actualStudentIncome) {
            return $actualStudentIncome;
        }

        $util = $this->util;

        $totalPayments = 0.0;

        foreach($this->studentParticipants as $studentParticipant) {
            if($studentParticipant->isNoLongerGoing() !== true) {
                $payment = $util->getStudentPayment($studentParticipant);
                $missingOsaConsent = $util->getMissingOSAConsent($studentParticipant);
                $isAbleToGo = $util->isAbleToGo($studentParticipant, $missingOsaConsent, $payment);
                if($isAbleToGo === true) {
                    $totalPayments += $payment['amountPaid'];
                }
            }
        }

        return $this->actualStudentIncome = $totalPayments;
    }

    public function getPlannedTotalIncome()
    {
        $income = $this->income;
        return $income->getIncomeStudent() + $income->getIncomeCollege() + $income->getIncomeOther();
    }

    public function getActualTotalIncome()
    {
        $income = $this->income;
        return $this->getActualStudentIncome() + $income->getIncomeCollege() + $income->getIncomeOther();
    }

    public function getTotalExpense()
    {
        $total = 0.0;

        foreach($this->expenses as $expense) {
            $total += $expense->getAmount();
        }

        return $total;
    }
}