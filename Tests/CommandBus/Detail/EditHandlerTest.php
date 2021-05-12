<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Detail;

use App\Entity\Employee\Employee;
use App\Entity\Misc\RiskAssessment;
use Cis\EducationalVisitBundle\CommandBus\Detail\EditCommand;
use Cis\EducationalVisitBundle\CommandBus\Detail\EditHandler;
use Cis\EducationalVisitBundle\Entity\Area;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Messenger\EducationalVisitMessenger;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use DateTime;
use Prophecy\Argument;

class EditHandlerTest extends TestCase
{
    private $orm;
    private $messenger;
    private $riskAssessment;
    // Organiser Section
    private $organiser;
    private $organiserMobile;
    private $secondOrganiserMobile;
    // Main Section
    private $title;
    private $location;
    private $category;
    private $osaRequired = true;
    private $area;
    private $description;
    private $showOnCollegeCalendar = true;
    private $riskAssessments;
    // Participants Section
    private $proposedNumberOfStudents;
    private $minimumNumberOfStudents;
    private $maximumNumberOfStudents;
    private $proposedNumberOfStaff;
    // Date Section
    private $academicYear;
    private $startDate;
    private $endDate;
    private $startTime;
    private $endTime;
    private $recurrencePattern;
    // Emergency Contact Section
    private $emergencyContact;
    private $secondEmergencyContactName;
    private $secondEmergencyContactLandline;
    private $secondEmergencyContactMobile;
    // Payment Section
    private $fullPaymentAmount;
    private $fullPaymentDeadline;
    private $firstPaymentAmount;
    private $firstPaymentDeadline;
    private $paymentCompleteEmail;
    private $replyToEmailAddress;
    private $emailSubject;
    private $emailContent;

    protected function setUp()
    {
        $this->orm = $this->prophesize(Orm::class);
        $this->messenger = $this->prophesize(EducationalVisitMessenger::class);
        $riskAssessment = $this->prophesize(RiskAssessment::class)->reveal();
        // Organiser Section
        $this->organiser = $this->prophesize(Employee::class)->reveal();
        $this->organiserMobile = '073647586945';
        $this->secondOrganiserMobile = '073647586567';
        // Main Section
        $this->title = 'Title';
        $this->location = 'location';
        $this->category = Visit::CATEGORY_DAY_TRIP_LR;
        $this->osaRequired = true;
        $this->area = $this->prophesize(Area::class)->reveal();
        $this->description = 'Description';
        $this->showOnCollegeCalendar = true;
        $this->riskAssessments = [$riskAssessment];
        // Participants Section
        $this->proposedNumberOfStudents = 20;
        $this->minimumNumberOfStudents = 10;
        $this->maximumNumberOfStudents = 25;
        $this->proposedNumberOfStaff = 2;
        // Date Section
        $this->academicYear = 2020;
        $this->startDate = $this->prophesize(DateTime::class)->reveal();
        $this->endDate = $this->prophesize(DateTime::class)->reveal();
        $this->startTime = $this->prophesize(DateTime::class)->reveal();
        $this->endTime = $this->prophesize(DateTime::class)->reveal();
        $this->recurrencePattern = 'Mon';
        // Emergency Contact Section
        $this->emergencyContact = $this->prophesize(Employee::class)->reveal();
        $this->secondEmergencyContactName = 'Second Emergency Contact Name';
        $this->secondEmergencyContactLandline = '073648567845';
        $this->secondEmergencyContactMobile = '0274658698559';
        // Payment Section
        $this->fullPaymentAmount = 30.25;
        $this->fullPaymentDeadline = $this->prophesize(DateTime::class)->reveal();
        $this->firstPaymentAmount = 10.25;
        $this->firstPaymentDeadline = $this->prophesize(DateTime::class)->reveal();
        $this->paymentCompleteEmail = true;
        $this->replyToEmailAddress = 'Reply To Email Address';
        $this->emailSubject = 'Email Subject';
        $this->emailContent = 'Email Content';
    }

    private function createHandler()
    {
        return new EditHandler($this->orm->reveal(), $this->messenger->reveal());
    }

    public function testAddHandle()
    {
        $command = $this->setCommandFields(new EditCommand());

        $this->orm
            ->persist(Argument::type(Visit::class))
            ->shouldBeCalled()
        ;

        $this->messenger
            ->sendFinanceCodesEmail(Argument::type(Visit::class))
            ->shouldBeCalled()
        ;

        $handler = $this->createHandler();
        $visit = $handler->handle($command);

        $this->assertVisitSame($visit);
    }

    public function testEditHandle()
    {
        $visit = new Visit(
            2020,
            'Test Title',
                'Test Category',
            'Test Description',
            'Test Location',
            $this->prophesize(Area::class)->reveal(),
            $this->prophesize(Employee::class)->reveal(),
            $this->prophesize(DateTime::class)->reveal(),
            $this->prophesize(DateTime::class)->reveal(),
            $this->prophesize(DateTime::class)->reveal()
        );

        $visit->addFinanceCodes('EDFFG', 'FFGBF');

        $command = $this->setCommandFields(new EditCommand($visit));

        $this->orm
            ->persist(Argument::type(Visit::class))
            ->shouldNotBeCalled()
        ;

        $this->messenger
            ->sendFinanceCodesEmail(Argument::type(Visit::class))
            ->shouldNotBeCalled()
        ;

        $this->messenger
            ->sendFinancePaymentAmountChangeEmail(Argument::type(Visit::class), Argument::type(Visit::class))
            ->shouldBeCalled()
        ;

        $this->messenger
            ->sendDetailsChangedOnApprovedVisit(Argument::type(Visit::class), Argument::type(Visit::class))
            ->shouldBeCalled()
        ;

        $handler = $this->createHandler();
        $visit = $handler->handle($command);

        $this->assertVisitSame($visit);
    }

    public function testDeletePaymentCompleteEmailHandle()
    {
        $visit = new Visit(
            2020,
            'Test Title',
            'Test Category',
            'Test Description',
            'Test Location',
            $this->prophesize(Area::class)->reveal(),
            $this->prophesize(Employee::class)->reveal(),
            $this->prophesize(DateTime::class)->reveal(),
            $this->prophesize(DateTime::class)->reveal(),
            $this->prophesize(DateTime::class)->reveal()
        );

        $command = $this->setCommandFields(new EditCommand($visit));
        $command->paymentCompleteEmail = false;

        $this->orm
            ->persist(Argument::type(Visit::class))
            ->shouldNotBeCalled()
        ;

        $handler = $this->createHandler();
        $visit = $handler->handle($command);

        $this->assertNull($visit->getPaymentCompleteEmailContent());
        $this->assertNull($visit->getPaymentCompleteEmailSubject());
        $this->assertNull($visit->getPaymentCompleteEmailReplyTo());
    }

    public function testPaymentNotRequiredHandle()
    {
        $visit = new Visit(
            2020,
            'Test Title',
            'Test Category',
            'Test Description',
            'Test Location',
            $this->prophesize(Area::class)->reveal(),
            $this->prophesize(Employee::class)->reveal(),
            $this->prophesize(DateTime::class)->reveal(),
            $this->prophesize(DateTime::class)->reveal(),
            $this->prophesize(DateTime::class)->reveal()
        );

        $visit->addFinanceCodes('EDFFG', 'FFGBF');

        $command = $this->setCommandFields(new EditCommand($visit));
        $command->paymentRequired = false;

        $this->orm
            ->persist(Argument::type(Visit::class))
            ->shouldNotBeCalled()
        ;

        $this->messenger
            ->sendFinanceCodesEmail(Argument::type(Visit::class))
            ->shouldNotBeCalled()
        ;

        $this->messenger
            ->sendFinancePaymentAmountChangeEmail(Argument::type(Visit::class), Argument::type(Visit::class))
            ->shouldBeCalled()
        ;

        $this->messenger
            ->sendDetailsChangedOnApprovedVisit(Argument::type(Visit::class), Argument::type(Visit::class))
            ->shouldBeCalled()
        ;

        $handler = $this->createHandler();
        $visit = $handler->handle($command);

        $this->assertSame(0.0, $visit->getFullPaymentAmount());
        $this->assertSame(null, $visit->getFullPaymentDeadline());
        $this->assertSame(0.0, $visit->getFirstPaymentAmount());
        $this->assertSame(null, $visit->getFirstPaymentDeadline());
    }

    protected function setCommandFields($command)
    {
        $command->organiser = $this->organiser;
        $command->organiserMobile = $this->organiserMobile;
        $command->secondOrganiserMobile = $this->secondOrganiserMobile;
        // Main Section
        $command->title = $this->title;
        $command->location = $this->location;
        $command->category = $this->category;
        $command->osaRequired = $this->osaRequired ;
        $command->area = $this->area ;
        $command->description = $this->description ;
        $command->showOnCollegeCalendar = $this->showOnCollegeCalendar;
        $command->riskAssessments = $this->riskAssessments;
        // Participants Section
        $command->proposedNumberOfStudents = $this->proposedNumberOfStudents;
        $command->minimumNumberOfStudents = $this->minimumNumberOfStudents;
        $command->maximumNumberOfStudents = $this->maximumNumberOfStudents;
        $command->proposedNumberOfStaff = $this->proposedNumberOfStaff;
        // Date Section
        $command->academicYear = $this->academicYear;
        $command->startDate = $this->startDate;
        $command->endDate = $this->endDate;
        $command->startTime = $this->startTime;
        $command->endTime = $this->endTime;
        $command->recurrencePattern = $this->recurrencePattern;
        // Emergency Contact Section
        $command->emergencyContact = $this->emergencyContact;
        $command->secondEmergencyContactName = $this->secondEmergencyContactName;
        $command->secondEmergencyContactLandline = $this->secondEmergencyContactLandline;
        $command->secondEmergencyContactMobile = $this->secondEmergencyContactMobile;
        // Payment Section
        $command->paymentRequired = true;
        $command->fullPaymentAmount = $this->fullPaymentAmount;
        $command->fullPaymentDeadline = $this->fullPaymentDeadline;
        $command->firstPaymentAmount = $this->firstPaymentAmount;
        $command->firstPaymentDeadline = $this->firstPaymentDeadline;
        $command->paymentCompleteEmail = $this->paymentCompleteEmail;
        $command->replyToEmailAddress = $this->replyToEmailAddress;
        $command->emailSubject = $this->emailSubject;
        $command->emailContent = $this->emailContent ;

        return $command;
    }

    protected function assertVisitSame(Visit $visit)
    {
        $this->assertSame($this->organiser, $visit->getOrganiser());
        $this->assertSame($this->organiserMobile, $visit->getOrganiserMobile());
        $this->assertSame($this->secondOrganiserMobile, $visit->getOrganiserMobileSecond());
        $this->assertSame($this->title, $visit->getTitle());
        $this->assertSame($this->location, $visit->getLocation());
        $this->assertSame($this->category, $visit->getCategory());
        $this->assertSame($this->osaRequired, $visit->isOsaRequired());
        $this->assertSame($this->area, $visit->getArea());
        $this->assertSame($this->description, $visit->getDescription());
        $this->assertSame($this->showOnCollegeCalendar, $visit->isToBeShownOnCalendar());
        $this->assertSame($this->riskAssessments, $visit->getRiskAssessments());
        $this->assertSame($this->proposedNumberOfStudents, $visit->getProposedNoStudents());
        $this->assertSame($this->minimumNumberOfStudents, $visit->getMinimumStudents());
        $this->assertSame($this->maximumNumberOfStudents, $visit->getMaximumStudents());
        $this->assertSame($this->proposedNumberOfStaff, $visit->getProposedNoStaff());
        $this->assertSame($this->academicYear, $visit->getAcademicYear());
        $this->assertSame($this->startDate, $visit->getStartDate());
        $this->assertSame($this->endDate, $visit->getEndDate());
        $this->assertSame($this->startTime, $visit->getStartTime());
        $this->assertSame($this->endTime, $visit->getEndTime());
        $this->assertSame($this->recurrencePattern, $visit->getRecurrencePattern());
        $this->assertSame($this->emergencyContact, $visit->getEmergencyContact());
        $this->assertSame($this->secondEmergencyContactName, $visit->getSecondContactName());
        $this->assertSame($this->secondEmergencyContactLandline, $visit->getSecondContactNumber());
        $this->assertSame($this->secondEmergencyContactMobile, $visit->getSecondContactMobile());
        $this->assertSame($this->fullPaymentAmount, $visit->getFullPaymentAmount());
        $this->assertSame($this->fullPaymentDeadline, $visit->getFullPaymentDeadline());
        $this->assertSame($this->firstPaymentAmount, $visit->getFirstPaymentAmount());
        $this->assertSame($this->firstPaymentDeadline, $visit->getFirstPaymentDeadline());
        $this->assertSame($this->replyToEmailAddress, $visit->getPaymentCompleteEmailReplyTo());
        $this->assertSame($this->emailSubject, $visit->getPaymentCompleteEmailSubject());
        $this->assertSame($this->emailContent, $visit->getPaymentCompleteEmailContent());
    }

}