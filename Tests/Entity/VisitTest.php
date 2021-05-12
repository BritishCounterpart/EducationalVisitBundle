<?php

namespace Cis\EducationalVisitBundle\Tests\Entity;

use App\Entity\Calendar\Event;
use App\Entity\Employee\Employee;
use App\Entity\Misc\RiskAssessment;
use App\Entity\User;
use Cis\EducationalVisitBundle\Entity\Area;
use Cis\EducationalVisitBundle\Entity\Expense;
use Cis\EducationalVisitBundle\Entity\Income;
use Cis\EducationalVisitBundle\Entity\Itinerary;
use Cis\EducationalVisitBundle\Entity\StaffParticipant;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;
use DateTime;

class VisitTest extends TestCase
{
    private $area;
    private $organiser;
    private $employee;
    private $event;
    private $expense;
    private $income;
    private $studentParticipant;
    private $staffParticipant;
    private $itinerary;
    private $riskAssessment;

    protected function setUp()
    {
        $this->area = $this->prophesize(Area::class);
        $this->organiser = $this->prophesize(Employee::class);
        $this->employee = $this->prophesize(Employee::class);
        $this->event = $this->prophesize(Event::class);
        $this->expense = $this->prophesize(Expense::class);
        $this->income = $this->prophesize(Income::class);
        $this->studentParticipant = $this->prophesize(StudentParticipant::class);
        $this->staffParticipant = $this->prophesize(StaffParticipant::class);
        $this->itinerary = $this->prophesize(Itinerary::class);
        $this->riskAssessment = $this->prophesize(RiskAssessment::class);
    }

    private function createVisit()
    {
        return new Visit(
            2019,
            'Bude Trip',
            Visit::CATEGORY_DAY_TRIP_LR,
            'Trip to Bude',
            'Bude',
            $this->area->reveal(),
            $this->organiser->reveal(),
            new DateTime,
            new DateTime,
            new DateTime
        );
    }

    public function testConstants()
    {
        // Cohort
        $this->assertSame('educational_visit', Visit::COHORT_OBJECT);

        // Statues
        $this->assertSame('Approved', Visit::STATUS_APPROVED);
        $this->assertSame('Cancelled', Visit::STATUS_CANCELLED);
        $this->assertSame('Completed', Visit::STATUS_COMPLETED);
        $this->assertSame('Not Approved', Visit::STATUS_NOT_APPROVED);
        $this->assertSame('Pending Approval', Visit::STATUS_PENDING_APPROVAL);
        $this->assertSame('Planned', Visit::STATUS_PLANNED);

        // Issues
        $this->assertSame('Pending Risk Assessment', Visit::ISSUE_PEND_RISK_ASSESSMENT);
        $this->assertSame('Pending Expenses', Visit::ISSUE_PEND_EXPENSES);
        $this->assertSame('Pending Participants', Visit::ISSUE_PEND_PARTICIPANTS);
        $this->assertSame('Pending Payments', Visit::ISSUE_PEND_PAYMENTS);
        $this->assertSame('Pending Finance Codes', Visit::ISSUE_PEND_FINANCE_CODES);

        // Categories
        $this->assertSame('DTLR', Visit::CATEGORY_DAY_TRIP_LR);
        $this->assertSame('ODTHR', Visit::CATEGORY_OVERNIGHT_DAY_TRIP_HR);
        $this->assertSame('OHR', Visit::CATEGORY_OVERSEAS_HR);

        // Validation
        $this->assertSame(200, Visit::MAX_LENGTH_TITLE);
        $this->assertSame(150, Visit::MAX_LENGTH_LOCATION);
        $this->assertSame(12, Visit::MAX_LENGTH_ORGANISER_MOBILE);
        $this->assertSame(12, Visit::MAX_LENGTH_ORGANISER_MOBILE_SECOND);
        $this->assertSame(10, Visit::MAX_LENGTH_EV_NUMBER);
        $this->assertSame(20, Visit::MAX_LENGTH_COST_CODE);
        $this->assertSame(100, Visit::MAX_LENGTH_SECOND_CONTACT_NAME);
        $this->assertSame(12, Visit::MAX_LENGTH_SECOND_CONTACT_NUMBER);
        $this->assertSame(12, Visit::MAX_LENGTH_SECOND_CONTACT_MOBILE);
        $this->assertSame(100, Visit::MAX_LENGTH_PAYMENT_COMPLETE_EMAIL_SUBJECT);
        $this->assertSame(4000, Visit::MAX_LENGTH_PAYMENT_COMPLETE_EMAIL_CONTENT);
        $this->assertSame(100, Visit::MAX_LENGTH_PAYMENT_COMPLETE_EMAIL_REPLY_TO);
    }

    public function testConstructor()
    {
        $status = Visit::STATUS_PLANNED;
        $academicYear = 2020;
        $title = 'Spain Trip';
        $category = Visit::CATEGORY_OVERSEAS_HR;
        $description = 'Trip to Spain';
        $location = 'Spain';
        $area = $this->area->reveal();
        $organiser = $this->organiser->reveal();
        $startDate = new DateTime;
        $startTime = new DateTime;
        $endTime = new DateTime;

        $issues = [
            Visit::ISSUE_PEND_RISK_ASSESSMENT,
            Visit::ISSUE_PEND_EXPENSES,
            Visit::ISSUE_PEND_PARTICIPANTS
        ];

        $visit = new Visit(
            $academicYear,
            $title,
            $category,
            $description,
            $location,
            $area,
            $organiser,
            $startDate,
            $startTime,
            $endTime
        );

        $this->assertNull($visit->getId());
        $this->assertInstanceOf(DateTime::class, $visit->getCreatedOn());
        $this->assertSame($status, $visit->getStatus());
        $this->assertSame($academicYear, $visit->getAcademicYear());
        $this->assertSame($title, $visit->getTitle());
        $this->assertSame($category, $visit->getCategory());
        $this->assertSame($description, $visit->getDescription());
        $this->assertSame($location, $visit->getLocation());
        $this->assertSame($area, $visit->getArea());
        $this->assertSame($organiser, $visit->getOrganiser());
        $this->assertSame($startDate, $visit->getStartDate());
        $this->assertSame($startTime, $visit->getStartTime());
        $this->assertSame($endTime, $visit->getEndTime());
        $this->assertSame($issues, $visit->getIssues());
    }

    public function testSetAcademicYear()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'academicYear', 2021);
    }

    public function testSetTitle()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'title', 'Bideford Trip');
    }

    public function testSetCategory()
    {
        $visit = $this->createVisit();
        $visit->addStudentParticipant($this->prophesize(StudentParticipant::class)->reveal());
        $this->assertSetAndGet($visit, 'category', Visit::CATEGORY_DAY_TRIP_LR);
    }

    public function testSetDescription()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'description', 'Trip to Bideford');
    }

    public function testSetLocation()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'location', 'Bideford');
    }

    public function testSetStatus()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'status', Visit::STATUS_APPROVED);
    }

    public function testSetArea()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'area', $this->area->reveal());
    }

    public function testSetOrganiser()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'organiser', $this->organiser->reveal());
    }

    public function testSetOrganiserMobile()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'organiserMobile', '02385768574');
    }

    public function testSetOrganiserMobileSecond()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'organiserMobileSecond', '02385754567');
    }

    public function testSetStartDate()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'startDate', new DateTime);
    }

    public function testSetEndDate()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'endDate', new DateTime);
    }

    public function testSetStartTime()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'startTime', new DateTime);
    }

    public function testSetEndTime()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'endTime', new DateTime);
    }

    public function testSetRecurrencePattern()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'recurrencePattern', 'Mon');
    }

    public function testSetProposedNoStudents()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'proposedNoStudents', 30);
    }

    public function testSetProposedNoStaff()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'proposedNoStaff', 4);
    }

    public function testSetMinimumStudents()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'minimumStudents', 10);
    }

    public function testSetMaximumStudents()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'maximumStudents', 35);
    }

    public function testSetFullPaymentAmount()
    {
        $visit = $this->createVisit();

        $this->assertSetAndGet($visit, 'fullPaymentAmount', 300.0);
        $this->assertContains(Visit::ISSUE_PEND_FINANCE_CODES, $visit->getIssues());

        $this->assertSetAndGet($visit, 'fullPaymentAmount', 0.0);
        $this->assertNotContains(Visit::ISSUE_PEND_FINANCE_CODES, $visit->getIssues());
        $visit->addFinanceCodes('FSFFWF', 'FDDW');

        $this->assertSetAndGet($visit, 'fullPaymentAmount', 400.0);
        $this->assertNotContains(Visit::ISSUE_PEND_FINANCE_CODES, $visit->getIssues());

    }

    public function testSetFullPaymentDeadline()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'fullPaymentDeadline', new DateTime);
    }

    public function testSetFirstPaymentAmount()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'firstPaymentAmount', 100.0);
    }

    public function testSetFirstPaymentDeadline()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'firstPaymentDeadline', new DateTime);
    }

    public function testAddFinanceCodes()
    {
        $evNumber = 'DHFJR';
        $costCode = 'DHFJR';
        $visit = $this->createVisit();
        $visit->setFullPaymentAmount(300.0);

        $visit->addFinanceCodes(null, null);
        $this->assertContains(Visit::ISSUE_PEND_FINANCE_CODES, $visit->getIssues());
        $this->assertNull($visit->getEvNumber());
        $this->assertNull($visit->getCostCode());

        $visit->addFinanceCodes($evNumber, null);
        $this->assertContains(Visit::ISSUE_PEND_FINANCE_CODES, $visit->getIssues());
        $this->assertSame($evNumber, $visit->getEvNumber());
        $this->assertNull($visit->getCostCode());

        $visit->addFinanceCodes($evNumber, $costCode);
        $this->assertNotContains(Visit::ISSUE_PEND_FINANCE_CODES, $visit->getIssues());
        $this->assertSame($evNumber, $visit->getEvNumber());
        $this->assertSame($costCode, $visit->getCostCode());

        $visit->addFinanceCodes(null, $costCode);
        $this->assertContains(Visit::ISSUE_PEND_FINANCE_CODES, $visit->getIssues());
        $this->assertNull($visit->getEvNumber());
        $this->assertSame($costCode, $visit->getCostCode());
    }

    public function testSetRiskAssessments()
    {
        $riskAssessment = $this->riskAssessment->reveal();
        $visit = $this->createVisit();
        $visit->setRiskAssessments([$riskAssessment]);
        $this->assertContains($riskAssessment, $visit->getRiskAssessments());
        $this->assertCount(1, $visit->getRiskAssessments());
        $this->assertNotContains(Visit::ISSUE_PEND_RISK_ASSESSMENT, $visit->getIssues());
        $this->assertSetAndGet($visit, 'riskAssessments', []);
        $this->assertContains(Visit::ISSUE_PEND_RISK_ASSESSMENT, $visit->getIssues());
    }


    public function testSetEmergencyContact()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'emergencyContact', $this->employee->reveal());
    }

    public function testSetSecondContactName()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'secondContactName', "John");
    }

    public function testSetSecondContactNumber()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'secondContactNumber', "074658694756");
    }

    public function testSetSecondContactMobile()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'secondContactMobile', "074345678765");
    }

    public function testSetShowOnCalendar()
    {
        $visit = $this->createVisit();
        $visit->setShowOnCalendar(true);
        $this->assertTrue($visit->isToBeShownOnCalendar());
    }

    public function testSetEvent()
    {
        $visit = $this->createVisit();
        $this->assertSetAndGet($visit, 'event', $this->event->reveal());
    }

    public function testCreatePaymentCompleteEmail()
    {
        $subject = 'Test Subject';
        $replyTo = 'Test@replyTo.com';
        $content = 'Test Content';

        $visit = $this->createVisit();
        $visit->createPaymentCompleteEmail(
            $subject,
            $replyTo,
            $content
        );

        $this->assertSame($subject, $visit->getPaymentCompleteEmailSubject());
        $this->assertSame($replyTo, $visit->getPaymentCompleteEmailReplyTo());
        $this->assertSame($content, $visit->getPaymentCompleteEmailContent());
    }

    public function testDeletePaymentCompleteEmail()
    {
        $subject = 'Test Subject';
        $replyTo = 'Test@replyTo.com';
        $content = 'Test Content';

        $visit = $this->createVisit();
        $visit->createPaymentCompleteEmail(
            $subject,
            $replyTo,
            $content
        );

        $visit->deletePaymentCompleteEmail();

        $this->assertNull($visit->getPaymentCompleteEmailSubject());
        $this->assertNull($visit->getPaymentCompleteEmailReplyTo());
        $this->assertNull($visit->getPaymentCompleteEmailContent());
    }

    public function testSetHasExpenses()
    {
        $visit = $this->createVisit();
        $visit->setHasExpenses(true);
        $this->assertTrue($visit->hasExpenses());
        $this->assertContains(Visit::ISSUE_PEND_EXPENSES, $visit->getIssues());

        $visit = $this->createVisit();
        $visit->addExpense($this->expense->reveal());
        $visit->setHasExpenses(true);
        $this->assertTrue($visit->hasExpenses());
        $this->assertNotContains(Visit::ISSUE_PEND_EXPENSES, $visit->getIssues());

        $visit = $this->createVisit();
        $visit->addExpense($this->expense->reveal());
        $visit->setHasExpenses(false);
        $this->assertFalse($visit->hasExpenses());
        $this->assertNotContains(Visit::ISSUE_PEND_EXPENSES, $visit->getIssues());
        $this->assertCount(0, $visit->getExpenses());
    }

    public function testSetOsaRequired()
    {
        $visit = $this->createVisit();
        $visit->setOsaRequired(true);
        $this->assertTrue($visit->isOsaRequired());
    }

    public function testSetVisitFull()
    {
        $visit = $this->createVisit();
        $visit->setVisitFull(true);
        $this->assertTrue($visit->isVisitFull());
    }

    public function testIsVisitConsentRequired()
    {
        $visit = $this->createVisit();
        $visit->setCategory(Visit::CATEGORY_OVERNIGHT_DAY_TRIP_HR);
        $this->assertTrue($visit->isVisitConsentRequired());

        $visit = $this->createVisit();
        $visit->setCategory(Visit::CATEGORY_OVERSEAS_HR);
        $this->assertTrue($visit->isVisitConsentRequired());

        $visit = $this->createVisit();
        $visit->setCategory(Visit::CATEGORY_DAY_TRIP_LR);
        $this->assertFalse($visit->isVisitConsentRequired());
    }

    public function testAddExpense()
    {
        $expense = $this->expense->reveal();
        $visit = $this->createVisit();
        $visit->addExpense($expense);
        $this->assertContains($expense, $visit->getExpenses());
        $this->assertCount(1, $visit->getExpenses());
        $this->assertNotContains(Visit::ISSUE_PEND_EXPENSES, $visit->getIssues());
    }

    public function testRemoveExpense()
    {
        $expense = $this->expense->reveal();
        $visit = $this->createVisit();
        $visit->addExpense($expense);
        $visit->removeExpense($expense);
        $this->assertCount(0, $visit->getExpenses());
        $this->assertContains(Visit::ISSUE_PEND_EXPENSES, $visit->getIssues());
    }

    public function testGetIncome()
    {
        $visit = $this->createVisit();
        $income = $this->income->reveal();
        $this->setPropertyValue($visit, 'income', $income);
        $this->assertSame($income, $visit->getIncome());
    }

    public function testAddStudentParticipant()
    {
        $studentParticipant = $this->studentParticipant->reveal();
        $visit = $this->createVisit();
        $visit->addStudentParticipant($studentParticipant);
        $this->assertContains($studentParticipant, $visit->getStudentParticipants());
        $this->assertCount(1, $visit->getStudentParticipants());
        $this->assertNotContains(Visit::ISSUE_PEND_PARTICIPANTS, $visit->getIssues());
    }

    public function testRemoveStudentParticipant()
    {
        $studentParticipant = $this->studentParticipant->reveal();
        $visit = $this->createVisit();
        $visit->addStudentParticipant($studentParticipant);
        $visit->removeStudentParticipant($studentParticipant);
        $this->assertCount(0, $visit->getStudentParticipants());
        $this->assertContains(Visit::ISSUE_PEND_PARTICIPANTS, $visit->getIssues());
    }


    public function testAddStaffParticipant()
    {
        $staffParticipant = $this->staffParticipant->reveal();
        $visit = $this->createVisit();
        $visit->addStaffParticipant($staffParticipant);
        $this->assertContains($staffParticipant, $visit->getStaffParticipants());
        $this->assertCount(1, $visit->getStaffParticipants());
    }

    public function testRemoveStaffParticipant()
    {
        $staffParticipant = $this->staffParticipant->reveal();
        $visit = $this->createVisit();
        $visit->addStaffParticipant($staffParticipant);
        $visit->removeStaffParticipant($staffParticipant);
        $this->assertCount(0, $visit->getStaffParticipants());
    }

    public function testSetItinerary()
    {
        $visit = $this->createVisit();
        $itinerary = $this->itinerary->reveal();
        $this->assertSetAndGet($visit, 'itinerary', $itinerary);
    }

    public function testCanRequestApproval()
    {
        $visit = $this->createVisit();

        $visit->setStatus(Visit::STATUS_PENDING_APPROVAL);
        $this->assertFalse($visit->canRequestApproval());

        $visit->setStatus(Visit::STATUS_PLANNED);
        $visit->removeIssue(Visit::ISSUE_PEND_EXPENSES);
        $visit->removeIssue(Visit::ISSUE_PEND_RISK_ASSESSMENT);
        $this->assertTrue($visit->canRequestApproval());

        $visit->setStatus(Visit::STATUS_NOT_APPROVED);
        $this->assertTrue($visit->canRequestApproval());

        $visit->addIssue(Visit::ISSUE_PEND_RISK_ASSESSMENT);
        $this->assertFalse($visit->canRequestApproval());

        $visit->addIssue(Visit::ISSUE_PEND_RISK_ASSESSMENT);
        $visit->removeIssue(Visit::ISSUE_PEND_EXPENSES);
        $this->assertFalse($visit->canRequestApproval());
    }

    public function testCanApprove()
    {
        $user = $this->prophesize(User::class)->reveal();
        $user2 = $this->prophesize(User::class)->reveal();

        $this->area->getApprovalUsers()->willReturn([$user]);

        $visit = $this->createVisit();

        $visit->setStatus(Visit::STATUS_PENDING_APPROVAL);
        $this->assertTrue($visit->canApprove($user));

        $this->assertFalse($visit->canApprove($user2));

        $visit->setStatus(Visit::STATUS_APPROVED);
        $this->assertFalse($visit->canApprove($user));
    }
}