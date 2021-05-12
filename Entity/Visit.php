<?php

namespace Cis\EducationalVisitBundle\Entity;

use App\Entity\Calendar\Event;
use App\Entity\Employee\Employee;
use App\Entity\User;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

class Visit
{
    // Cohort
    const COHORT_OBJECT = 'educational_visit';

    // Statues
    const STATUS_APPROVED = 'Approved';
    const STATUS_CANCELLED = 'Cancelled';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_NOT_APPROVED = 'Not Approved';
    const STATUS_PENDING_APPROVAL = 'Pending Approval';
    const STATUS_PLANNED = 'Planned';

    // Issues
    const ISSUE_PEND_RISK_ASSESSMENT = 'Pending Risk Assessment';
    const ISSUE_PEND_EXPENSES = 'Pending Expenses';
    const ISSUE_PEND_PARTICIPANTS = 'Pending Participants';
    const ISSUE_PEND_PAYMENTS = 'Pending Payments';
    const ISSUE_PEND_FINANCE_CODES = 'Pending Finance Codes';

    // Categories
    const CATEGORY_DAY_TRIP_LR = 'DTLR';
    const CATEGORY_OVERNIGHT_DAY_TRIP_HR = 'ODTHR';
    const CATEGORY_OVERSEAS_HR = 'OHR';

    // Validation
    const MAX_LENGTH_TITLE = 200;
    const MAX_LENGTH_LOCATION = 150;
    const MAX_LENGTH_ORGANISER_MOBILE = 12;
    const MAX_LENGTH_ORGANISER_MOBILE_SECOND = 12;
    const MAX_LENGTH_EV_NUMBER = 10;
    const MAX_LENGTH_COST_CODE = 20;
    const MAX_LENGTH_SECOND_CONTACT_NAME = 100;
    const MAX_LENGTH_SECOND_CONTACT_NUMBER = 12;
    const MAX_LENGTH_SECOND_CONTACT_MOBILE = 12;
    const MAX_LENGTH_PAYMENT_COMPLETE_EMAIL_SUBJECT = 100;
    const MAX_LENGTH_PAYMENT_COMPLETE_EMAIL_CONTENT = 4000;
    const MAX_LENGTH_PAYMENT_COMPLETE_EMAIL_REPLY_TO = 100;

    private $id;
    private $createdOn;
    private $academicYear;
    private $title;
    private $category;
    private $description;
    private $location;
    private $status;
    private $area;
    private $organiser;
    private $organiserMobile;
    private $organiserMobileSecond;
    private $startDate;
    private $endDate;
    private $startTime;
    private $endTime;
    private $recurrencePattern;
    private $proposedNoStudents;
    private $proposedNoStaff;
    private $minimumStudents;
    private $maximumStudents;
    private $fullPaymentAmount;
    private $fullPaymentDeadline;
    private $firstPaymentAmount;
    private $firstPaymentDeadline;
    private $evNumber;
    private $costCode;
    private $riskAssessments;
    private $emergencyContact;
    private $secondContactName;
    private $secondContactNumber;
    private $secondContactMobile;
    private $showOnCalendar;
    private $event;
    private $paymentCompleteEmailSubject;
    private $paymentCompleteEmailContent;
    private $paymentCompleteEmailReplyTo;
    private $hasExpenses = true;
    private $osaRequired = false;
    private $visitFull = false;
    private $issues = [];
    private $expenses;
    private $income;
    private $studentParticipants;
    private $staffParticipants;
    private $itinerary;
    private $deletedOn;

    public function __construct(int $academicYear, string $title, string $category, string $description, string $location, Area $area, Employee $organiser, DateTime $startDate, DateTime $startTime, DateTime $endTime)
    {
        $this->createdOn = new DateTime;
        $this->status = self::STATUS_PLANNED;
        $this->addIssue(self::ISSUE_PEND_RISK_ASSESSMENT);
        $this->addIssue(self::ISSUE_PEND_EXPENSES);
        $this->addIssue(self::ISSUE_PEND_PARTICIPANTS);
        $this->addFinanceCodes(null, null);
        $this->expenses = new ArrayCollection();
        $this->studentParticipants = new ArrayCollection();
        $this->staffParticipants = new ArrayCollection();
        $this->riskAssessments = new ArrayCollection();
        $this->academicYear = $academicYear;
        $this->title = $title;
        $this->category = $category;
        $this->description = $description;
        $this->location = $location;
        $this->area = $area;
        $this->organiser = $organiser;
        $this->startDate = $startDate;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    public function setAcademicYear(int $academicYear)
    {
        $this->academicYear = $academicYear;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(string $category)
    {
        $this->category = $category;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation(string $location)
    {
        $this->location = $location;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
        return $this;
    }

    public function getArea()
    {
        return $this->area;
    }

    public function setArea(Area $area)
    {
        $this->area = $area;
        return $this;
    }

    public function getOrganiser()
    {
        return $this->organiser;
    }

    public function setOrganiser(Employee $organiser)
    {
        $this->organiser = $organiser;
        return $this;
    }

    public function getOrganiserMobile()
    {
        return $this->organiserMobile;
    }

    public function setOrganiserMobile(string $organiserMobile = null)
    {
        $this->organiserMobile = $organiserMobile;
        return $this;
    }

    public function getOrganiserMobileSecond()
    {
        return $this->organiserMobileSecond;
    }

    public function setOrganiserMobileSecond(string $organiserMobileSecond = null)
    {
        $this->organiserMobileSecond = $organiserMobileSecond;
        return $this;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setEndDate(DateTime $endDate = null)
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setStartTime(DateTime $startTime)
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getRecurrencePattern()
    {
        return $this->recurrencePattern;
    }

    public function setRecurrencePattern(string $recurrencePattern = null)
    {
        $this->recurrencePattern = $recurrencePattern;
        return $this;
    }

    public function getProposedNoStudents()
    {
        return $this->proposedNoStudents;
    }

    public function setProposedNoStudents(int $proposedNoStudents)
    {
        $this->proposedNoStudents = $proposedNoStudents;
        return $this;
    }

    public function getProposedNoStaff()
    {
        return $this->proposedNoStaff;
    }

    public function setProposedNoStaff(int $proposedNoStaff)
    {
        $this->proposedNoStaff = $proposedNoStaff;
        return $this;
    }

    public function getMinimumStudents()
    {
        return $this->minimumStudents;
    }

    public function setMinimumStudents(int $minimumStudents)
    {
        $this->minimumStudents = $minimumStudents;
        return $this;
    }

    public function getMaximumStudents()
    {
        return $this->maximumStudents;
    }

    public function setMaximumStudents(int $maximumStudents)
    {
        $this->maximumStudents = $maximumStudents;
        return $this;
    }

    public function getFullPaymentAmount()
    {
        return $this->fullPaymentAmount;
    }

    public function setFullPaymentAmount(float $fullPaymentAmount = null)
    {
        $this->fullPaymentAmount = $fullPaymentAmount;
        // Visits which need payments, require an EV number and cost code;
        if(($this->evNumber === null or $this->costCode === null) and $fullPaymentAmount > 0) {
            $this->addIssue(self::ISSUE_PEND_FINANCE_CODES);
        } else {
            $this->removeIssue(self::ISSUE_PEND_FINANCE_CODES);
        }
        return $this;
    }

    public function getFullPaymentDeadline()
    {
        return $this->fullPaymentDeadline;
    }

    public function setFullPaymentDeadline(DateTime $fullPaymentDeadline = null)
    {
        $this->fullPaymentDeadline = $fullPaymentDeadline;
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

    public function getFirstPaymentDeadline()
    {
        return $this->firstPaymentDeadline;
    }

    public function setFirstPaymentDeadline(DateTime $firstPaymentDeadline = null)
    {
        $this->firstPaymentDeadline = $firstPaymentDeadline;
        return $this;
    }

    public function addFinanceCodes(string $evNumber = null, string $costCode = null)
    {
        $this->evNumber = $evNumber;
        $this->costCode = $costCode;
        // Visits which need payments, require an EV number and cost code;
        if(($evNumber === null or $costCode === null) and $this->fullPaymentAmount > 0) {
            $this->addIssue(self::ISSUE_PEND_FINANCE_CODES);
        } else {
            $this->removeIssue(self::ISSUE_PEND_FINANCE_CODES);
        }
        return $this;
    }

    public function getEvNumber()
    {
        return $this->evNumber;
    }

    public function getCostCode()
    {
        return $this->costCode;
    }

    public function getRiskAssessments()
    {
        return $this->riskAssessments;
    }

    public function setRiskAssessments($riskAssessments)
    {
        // Add or remove issue
        if(count($riskAssessments) > 0) {
            $this->removeIssue(self::ISSUE_PEND_RISK_ASSESSMENT);
        } else {
            $this->addIssue(self::ISSUE_PEND_RISK_ASSESSMENT);
        }

        $this->riskAssessments = $riskAssessments;
        return $this;
    }

    public function getEmergencyContact()
    {
        return $this->emergencyContact;
    }

    public function setEmergencyContact(Employee $emergencyContact)
    {
        $this->emergencyContact = $emergencyContact;
        return $this;
    }

    public function getSecondContactName()
    {
        return $this->secondContactName;
    }

    public function setSecondContactName(string $secondContactName = null)
    {
        $this->secondContactName = $secondContactName;
        return $this;
    }

    public function getSecondContactNumber()
    {
        return $this->secondContactNumber;
    }

    public function setSecondContactNumber(string $secondContactNumber = null)
    {
        $this->secondContactNumber = $secondContactNumber;
        return $this;
    }

    public function getSecondContactMobile()
    {
        return $this->secondContactMobile;
    }

    public function setSecondContactMobile(string $secondContactMobile = null)
    {
        $this->secondContactMobile = $secondContactMobile;
        return $this;
    }

    public function isToBeShownOnCalendar()
    {
        return true === $this->showOnCalendar;
    }

    public function setShowOnCalendar(bool $showOnCalendar)
    {
        $this->showOnCalendar = $showOnCalendar;
        return $this;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function setEvent(Event $event = null)
    {
        $this->event = $event;
        return $this;
    }

    public function createPaymentCompleteEmail(string $subject, string $replyTo, string $content)
    {
        $this->paymentCompleteEmailSubject = $subject;
        $this->paymentCompleteEmailReplyTo = $replyTo;
        $this->paymentCompleteEmailContent = $content;
        return $this;
    }

    public function deletePaymentCompleteEmail()
    {
        $this->paymentCompleteEmailSubject = null;
        $this->paymentCompleteEmailReplyTo = null;
        $this->paymentCompleteEmailContent = null;
        return $this;
    }

    public function getPaymentCompleteEmailSubject()
    {
        return $this->paymentCompleteEmailSubject;
    }

    public function getPaymentCompleteEmailContent()
    {
        return $this->paymentCompleteEmailContent;
    }

    public function getPaymentCompleteEmailReplyTo()
    {
        return $this->paymentCompleteEmailReplyTo;
    }

    public function hasExpenses()
    {
        return true === $this->hasExpenses;
    }

    public function setHasExpenses(bool $hasExpenses)
    {
        $this->hasExpenses = $hasExpenses;

        // If flagged as having no expenses remove expense issue and expenses
        if($hasExpenses === false) {
            $this->removeIssue(self::ISSUE_PEND_EXPENSES);
            foreach($this->expenses as $expense) {
                $this->expenses->removeElement($expense);
            }
            return $this;
        }

        // If flagged as having expenses but no expenses exist else if expenses exist, remove issue
        if($hasExpenses === true and count($this->expenses) < 1) {
            $this->addIssue(self::ISSUE_PEND_EXPENSES);
            return $this;
        } else {
            $this->removeIssue(self::ISSUE_PEND_EXPENSES);
        }

        return $this;
    }

    public function isOsaRequired()
    {
        return true === $this->osaRequired;
    }

    public function setOsaRequired(bool $osaRequired)
    {
        $this->osaRequired = $osaRequired;
        return $this;
    }

    public function isVisitFull()
    {
        return true === $this->visitFull;
    }

    public function setVisitFull(bool $visitFull)
    {
        $this->visitFull = $visitFull;
        return $this;
    }

    public function isVisitConsentRequired()
    {
        $categories = [
            self::CATEGORY_OVERNIGHT_DAY_TRIP_HR,
            self::CATEGORY_OVERSEAS_HR
        ];

        if(($key = array_search($this->getCategory(), $categories)) !== false) {
            return true;
        }

        return false;
    }

    public function getIssues()
    {
        return $this->issues;
    }

    public function addIssue($issue)
    {
        if(($key = array_search($issue, $this->issues)) === false) {
            $this->issues[] = $issue;
        }

        return $this;
    }

    public function removeIssue($issue)
    {
        if(($key = array_search($issue, $this->issues)) !== false) {
            unset($this->issues[$key]);
        }

        return $this;
    }

    public function getExpenses()
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense)
    {
        $this->expenses[] = $expense;
        // Remove issue once there are expenses
        if(count($this->expenses) > 0) {
            $this->removeIssue(self::ISSUE_PEND_EXPENSES);
        }

        return $this;
    }

    public function removeExpense(Expense $expense)
    {
        $this->expenses->removeElement($expense);
        // Add issue if there are no expenses and visit marked as having expenses
        if(count($this->expenses) < 1 and $this->hasExpenses) {
            $this->addIssue(self::ISSUE_PEND_EXPENSES);
        }
        return $this;
    }

    public function getIncome()
    {
        return $this->income;
    }

    public function hasIncome()
    {
        if(null === $income = $this->income) {
            return false;
        }

        if($income->getIncomeStudent() > 0 or $income->getIncomeCollege()  > 0 or $income->getIncomeOther() > 0) {
            return true;
        }

        return false;
    }

    public function getStudentParticipants()
    {
        return $this->studentParticipants;
    }

    public function addStudentParticipant(StudentParticipant $studentParticipant)
    {
        $this->studentParticipants[] = $studentParticipant;
        // Remove issue once there are student participants
        if(count($this->studentParticipants) > 0) {
            $this->removeIssue(self::ISSUE_PEND_PARTICIPANTS);
        }

        return $this;
    }

    public function removeStudentParticipant(StudentParticipant $studentParticipant)
    {
        $this->studentParticipants->removeElement($studentParticipant);
        // Add issue if there are no student participants
        if(count($this->studentParticipants) < 1) {
            $this->addIssue(self::ISSUE_PEND_PARTICIPANTS);
        }
        return $this;
    }

    public function getStaffParticipants()
    {
        return $this->staffParticipants;
    }

    public function addStaffParticipant(StaffParticipant $staffParticipant)
    {
        $this->staffParticipants[] = $staffParticipant;
        return $this;
    }

    public function removeStaffParticipant(StaffParticipant $staffParticipant)
    {
        $this->staffParticipants->removeElement($staffParticipant);
        return $this;
    }

    public function getItinerary()
    {
        return $this->itinerary;
    }

    public function setItinerary(Itinerary $itinerary = null)
    {
        $this->itinerary = $itinerary;
        return $this;
    }

    public function canRequestApproval()
    {
        if(in_array($this->status,[self::STATUS_PLANNED, self::STATUS_NOT_APPROVED]) and !in_array(self::ISSUE_PEND_RISK_ASSESSMENT, $this->issues) and !in_array(self::ISSUE_PEND_EXPENSES, $this->issues)) {
            return true;
        }

        return false;
    }

    public function canApprove(User $user)
    {
        if(in_array($user, $this->area->getApprovalUsers()) and $this->status === self::STATUS_PENDING_APPROVAL) {
            return true;
        }

        return false;
    }
}