<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Detail;

use Cis\EducationalVisitBundle\CommandBus\Detail\EditCommand;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Bridge\PhpUnit\ValidatorTrait;
use DateTime;

class EditCommandTest extends TestCase
{
    use ValidatorTrait;

    protected function createCommand()
    {
        $command = new EditCommand();
        $command->organiser = 'Organiser';
        $command->title = 'Title';
        $command->location = 'Location';
        $command->category = Visit::CATEGORY_DAY_TRIP_LR;
        $command->area = 'Area';
        $command->description = 'Description';
        $command->showOnCollegeCalendar = true;
        $command->proposedNumberOfStudents = 20;
        $command->minimumNumberOfStudents = 10;
        $command->maximumNumberOfStudents = 25;
        $command->proposedNumberOfStaff = 2;
        $command->academicYear = 2020;
        $command->startDate = new DateTime;
        $command->startTime = new DateTime;
        $command->endTime = new DateTime;
        $command->emergencyContact = 'Emergency Contact';
        $command->paymentCompleteEmail = false;
        return $command;
    }

    public function testConstructor()
    {
        $visit = $this->prophesize(Visit::class);
        // Organiser Section
        $organiser = 'organiser';
        $visit->getOrganiser()->willReturn($organiser);
        $organiserMobile = 'organiserMobile';
        $visit->getOrganiserMobile()->willReturn($organiserMobile);
        $secondOrganiserMobile = 'secondOrganiserMobile';
        $visit->getOrganiserMobileSecond()->willReturn($secondOrganiserMobile);
        // Main Section
        $title = 'title';
        $visit->getTitle()->willReturn($title);
        $location = 'location';
        $visit->getLocation()->willReturn($location);
        $category = 'category';
        $visit->getCategory()->willReturn($category);
        $osaRequired = true;
        $visit->isOsaRequired()->willReturn($osaRequired);
        $area = 'area';
        $visit->getArea()->willReturn($area);
        $description = 'description';
        $visit->getDescription()->willReturn($description);
        $toBeShownOnCalendar = true;
        $visit->isToBeShownOnCalendar()->willReturn($toBeShownOnCalendar);
        $riskAssessments = ['riskAssessments'];
        $visit->getRiskAssessments()->willReturn($riskAssessments);
        // Participants Section
        $proposedNoStudents = 10;
        $visit->getProposedNoStudents()->willReturn($proposedNoStudents);
        $minimumStudents = 11;
        $visit->getMinimumStudents()->willReturn($minimumStudents);
        $maximumStudents = 12;
        $visit->getMaximumStudents()->willReturn($maximumStudents);
        $proposedNoStaff = 13;
        $visit->getProposedNoStaff()->willReturn($proposedNoStaff);
        // Date Section
        $academicYear = 2020;
        $visit->getAcademicYear()->willReturn($academicYear);
        $startDate = new DateTime;
        $visit->getStartDate()->willReturn($startDate);
        $endDate = new DateTime;
        $visit->getEndDate()->willReturn($endDate);
        $startTime = new DateTime;
        $visit->getStartTime()->willReturn($startTime);
        $endTime = new DateTime;
        $visit->getEndTime()->willReturn($endTime);
        $recurrencePattern = 'Mon';
        $visit->getRecurrencePattern()->willReturn($recurrencePattern);
        // Emergency Contact Section
        $emergencyContact = 'emergencyContact';
        $visit->getEmergencyContact()->willReturn($emergencyContact);
        $secondContactName = 'secondContactName';
        $visit->getSecondContactName()->willReturn($secondContactName);
        $secondContactNumber = 'secondContactNumber';
        $visit->getSecondContactNumber()->willReturn($secondContactNumber);
        $secondContactMobile = 'secondContactMobile';
        $visit->getSecondContactMobile()->willReturn($secondContactMobile);
        // Payment Section
        $fullPaymentAmount = 10.80;
        $visit->getFullPaymentAmount()->willReturn($fullPaymentAmount);
        $fullPaymentDeadline = new DateTime;
        $visit->getFullPaymentDeadline()->willReturn($fullPaymentDeadline);
        $firstPaymentAmount = 11.80;
        $visit->getFirstPaymentAmount()->willReturn($firstPaymentAmount);
        $firstPaymentDeadline = new DateTime;
        $visit->getFirstPaymentDeadline()->willReturn($firstPaymentDeadline);
        $paymentCompleteEmailReplyTo = 'paymentCompleteEmailReplyTo';
        $visit->getPaymentCompleteEmailReplyTo()->willReturn($paymentCompleteEmailReplyTo);
        $paymentCompleteEmailSubject = 'paymentCompleteEmailSubject';
        $visit->getPaymentCompleteEmailSubject()->willReturn($paymentCompleteEmailSubject);
        $paymentCompleteEmailContent = 'paymentCompleteEmailContent';
        $visit->getPaymentCompleteEmailContent()->willReturn($paymentCompleteEmailContent);

        $visit = $visit->reveal();

        $command = new EditCommand($visit);

        $this->assertSame($visit, $command->getVisit());
        // Organiser Section
        $this->assertSame($organiser, $command->organiser);
        $this->assertSame($organiserMobile, $command->organiserMobile);
        $this->assertSame($secondOrganiserMobile, $command->secondOrganiserMobile);
        // Main Section
        $this->assertSame($title, $command->title);
        $this->assertSame($location, $command->location);
        $this->assertSame($category, $command->category);
        $this->assertSame($osaRequired, $command->osaRequired);
        $this->assertSame($area, $command->area);
        $this->assertSame($description, $command->description);
        $this->assertSame($toBeShownOnCalendar, $command->showOnCollegeCalendar);
        $this->assertSame($riskAssessments, $command->riskAssessments);
        // Participants Section
        $this->assertSame($proposedNoStudents, $command->proposedNumberOfStudents);
        $this->assertSame($minimumStudents, $command->minimumNumberOfStudents);
        $this->assertSame($maximumStudents, $command->maximumNumberOfStudents);
        $this->assertSame($proposedNoStaff, $command->proposedNumberOfStaff);
        // Date Section
        $this->assertSame($academicYear, $command->academicYear);
        $this->assertSame($startDate, $command->startDate);
        $this->assertSame($endDate, $command->endDate);
        $this->assertSame($startTime, $command->startTime);
        $this->assertSame($endTime, $command->endTime);
        $this->assertSame($recurrencePattern, $command->recurrencePattern);
        // Emergency Contact Section
        $this->assertSame($emergencyContact, $command->emergencyContact);
        $this->assertSame($secondContactName, $command->secondEmergencyContactName);
        $this->assertSame($secondContactNumber, $command->secondEmergencyContactLandline);
        $this->assertSame($secondContactMobile, $command->secondEmergencyContactMobile);
        // Payment Section
        $this->assertSame($fullPaymentAmount, $command->fullPaymentAmount);
        $this->assertSame($fullPaymentDeadline, $command->fullPaymentDeadline);
        $this->assertSame($firstPaymentAmount, $command->firstPaymentAmount);
        $this->assertSame($firstPaymentDeadline, $command->firstPaymentDeadline);
        $this->assertTrue($command->paymentCompleteEmail);
        $this->assertSame($paymentCompleteEmailReplyTo, $command->replyToEmailAddress);
        $this->assertSame($paymentCompleteEmailSubject, $command->emailSubject);
        $this->assertSame($paymentCompleteEmailContent, $command->emailContent);

    }

    public function testSetVisit()
    {
        $visit = $this->prophesize(Visit::class)->reveal();
        $command = new EditCommand();
        $this->assertSetAndGet($command, 'visit', $visit);
    }

    public function testLoadValidatorMetadata()
    {
        $this->assertCanLoadValidatorMetadata(EditCommand::class);
    }

    /**
     * @dataProvider getValidationData
     */
    public function testValidation($command, $numViolations)
    {
        $this->setUpValidator();
        $this->assertCount(
            $numViolations,
            $this->getValidator()->validate($command)
        );
        $this->tearDownValidator();
    }

    public function getValidationData()
    {
        $command = $this->createCommand();
        yield [$command, 0];

        $command = $this->createCommand();
        $command->organiser = null;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->title = null;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->location = null;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->category = null;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->area = null;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->description = null;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->proposedNumberOfStudents = null;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->minimumNumberOfStudents = null;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->maximumNumberOfStudents = null;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->academicYear = null;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->startDate = null;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->startTime = null;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->endTime = null;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->emergencyContact = null;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->category = Visit::CATEGORY_OVERSEAS_HR;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->secondEmergencyContactName = 'Second Emergency Contact Name';
        yield [$command, 1];

        $command = $this->createCommand();
        $command->fullPaymentAmount = 30;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->firstPaymentAmount = 30;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->paymentCompleteEmail = true;
        yield [$command, 1];

        $command = $this->createCommand();
        $command->paymentCompleteEmail = true;
        $command->fullPaymentAmount = 30;
        $command->fullPaymentDeadline = new DateTime;
        yield [$command, 3];
    }
}