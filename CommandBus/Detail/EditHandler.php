<?php

namespace Cis\EducationalVisitBundle\CommandBus\Detail;

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
        // Handle adding a new visit or editing an existing visit
        if (null === $visit = $command->getVisit()) {
            $visit = new Visit(
                $command->academicYear,
                $command->title,
                $command->category,
                $command->description,
                $command->location,
                $command->area,
                $command->organiser,
                $command->startDate,
                $command->startTime,
                $command->endTime
            );
        } else {
            $original = clone $visit;
            $visit->setAcademicYear($command->academicYear);
            $visit->setTitle($command->title);
            $visit->setCategory($command->category);
            $visit->setDescription($command->description);
            $visit->setLocation($command->location);
            $visit->setArea($command->area);
            $visit->setOrganiser($command->organiser);
            $visit->setStartDate($command->startDate);
            $visit->setStartTime($command->startTime);
            $visit->setEndTime($command->endTime);
        }

        // Organiser Section
        $visit->setOrganiserMobile($command->organiserMobile);
        $visit->setOrganiserMobileSecond($command->secondOrganiserMobile);
        // Main Section
        $visit->setOsaRequired($command->osaRequired);
        $visit->setShowOnCalendar($command->showOnCollegeCalendar);
        $visit->setRiskAssessments($command->riskAssessments);
        // Participants Section
        $visit->setProposedNoStudents($command->proposedNumberOfStudents);
        $visit->setMinimumStudents($command->minimumNumberOfStudents);
        $visit->setMaximumStudents($command->maximumNumberOfStudents);
        $visit->setProposedNoStaff($command->proposedNumberOfStaff);
        // Date Section
        $visit->setEndDate($command->endDate);
        $visit->setRecurrencePattern($command->recurrencePattern);
        // Emergency Contact Section
        $visit->setEmergencyContact($command->emergencyContact);
        $visit->setSecondContactName($command->secondEmergencyContactName);
        $visit->setSecondContactNumber($command->secondEmergencyContactLandline);
        $visit->setSecondContactMobile($command->secondEmergencyContactMobile);
        // Payment Section
        if ($command->paymentRequired) {
            $visit->setFullPaymentAmount($command->fullPaymentAmount);
            $visit->setFullPaymentDeadline($command->fullPaymentDeadline);
            $visit->setFirstPaymentAmount($command->firstPaymentAmount);
            $visit->setFirstPaymentDeadline($command->firstPaymentDeadline);
        } else {
            $visit->setFullPaymentAmount(0);
            $visit->setFullPaymentDeadline(null);
            $visit->setFirstPaymentAmount(0);
            $visit->setFirstPaymentDeadline(null);
        }


        if ($command->paymentCompleteEmail) {
            $visit->createPaymentCompleteEmail(
                $command->emailSubject,
                $command->replyToEmailAddress,
                $command->emailContent
            );
        } else {
            $visit->deletePaymentCompleteEmail();
        }

        if (null === $command->getVisit()) {
            $this->orm->persist($visit);
        }

        // Send email to finance if there is an amount to be paid but no cost code or ev number yet
        if (($visit->getCostCode() === null or $visit->getEvNumber() === null) and $command->paymentRequired) {
            $this->messenger->sendFinanceCodesEmail($visit);
        }

        // Send email to finance if there is a change in the payment amount
        if (($visit->getCostCode() !== null or $visit->getEvNumber() !== null) and isset($original)) {
            if($original->getFullPaymentAmount() !== $visit->getFullPaymentAmount()) {
                $this->messenger->sendFinancePaymentAmountChangeEmail($visit, $original);
            }
        }

        if(isset($original)) {
            if ($original->getCategory() !== $visit->getCategory() or
                $original->getLocation() !== $visit->getLocation() or
                $original->getStartDate() !== $visit->getStartDate() or
                $original->getStartTime() !== $visit->getStartTime() or
                $original->getEndDate() !== $visit->getEndDate() or
                $original->getEndTime() !== $visit->getEndTime() or
                $original->getFullPaymentAmount() !== $visit->getFullPaymentAmount()
            ) {
                $this->messenger->sendDetailsChangedOnApprovedVisit($original, $visit);
            }
        }

        $command->setVisit($visit);

        return $visit;
    }
}