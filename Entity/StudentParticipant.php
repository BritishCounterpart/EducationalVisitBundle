<?php

namespace Cis\EducationalVisitBundle\Entity;

use App\Entity\Cohort\Cohort;
use App\Entity\Student\Student;
use DateTime;

class StudentParticipant
{
    private $id;
    private $createdOn;
    private $visit;
    private $student;
    private $cohort;
    private $fullPaymentAmount;
    private $firstPaymentAmount;
    private $noLongerGoing = false;
    private $paymentCompleteEmailSent = false;
    private $hasVisitConsent = false;
    private $deletedOn;

    public function __construct(Visit $visit, Student $student)
    {
        $this->createdOn = new DateTime;
        $this->visit = $visit;
        $this->student = $student;
        $visit->addStudentParticipant($this);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    public function getVisit()
    {
        return $this->visit;
    }

    public function getStudent()
    {
        return $this->student;
    }

    public function getCohort()
    {
        return $this->cohort;
    }

    public function setCohort(Cohort $cohort)
    {
        $this->cohort = $cohort;
        return $this;
    }

    public function getFullPaymentAmount()
    {
        return $this->fullPaymentAmount;
    }

    public function setFullPaymentAmount(float $fullPaymentAmount = null)
    {
        $this->fullPaymentAmount = $fullPaymentAmount;
        return $this;
    }

    public function getFirstPaymentAmount()
    {
        return $this->firstPaymentAmount;
    }

    public function setFirstPaymentAmount(float $firstPaymentAmount = null)
    {
        $this->firstPaymentAmount = $firstPaymentAmount;
        return $this;
    }

    public function isNoLongerGoing()
    {
        return $this->noLongerGoing;
    }

    public function setNoLongerGoing(bool $noLongerGoing)
    {
        $this->noLongerGoing = $noLongerGoing;
        return $this;
    }

    public function isPaymentCompleteEmailSent()
    {
        return $this->paymentCompleteEmailSent;
    }

    public function setPaymentCompleteEmailSent(bool $paymentCompleteEmailSent)
    {
        $this->paymentCompleteEmailSent = $paymentCompleteEmailSent;
        return $this;
    }

    public function setHasVisitConsent(bool $hasVisitConsent) {
        $this->hasVisitConsent = $hasVisitConsent;
        return $this;
    }

    public function hasVisitConsent()
    {
        $hasVisitConsent = $this->hasVisitConsent;
        $age = $this->getStudent()->getAgeToday();

        if(!$this->isVisitConsentRequired() or $age > 17) {
            return 'N/A';
        }

        if($this->isVisitConsentRequired() and $hasVisitConsent) {
            return 'Yes';
        }

        return 'No';
    }

    public function isVisitConsentRequired()
    {
        $categories = [
            Visit::CATEGORY_OVERNIGHT_DAY_TRIP_HR,
            Visit::CATEGORY_OVERSEAS_HR
        ];

        if($this->student->getAgeToday() > 17) {
            return false;
        }

        if(($key = array_search($this->visit->getCategory(), $categories)) !== false) {
            return true;
        }

        return false;
    }

}