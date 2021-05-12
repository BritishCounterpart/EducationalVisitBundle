<?php

namespace Cis\EducationalVisitBundle\CommandBus\Detail;

use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Component\CommandBus\Command;
use Petroc\CoreBundle\Validator\Constraint\MobileNumber;
use Petroc\CoreBundle\Validator\Constraint\PhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class EditCommand extends Command implements GroupSequenceProviderInterface
{
    const DEFAULT_GROUP = 'default';
    const HIGH_RISK_SECOND_EMR_CONTACT_GROUP = 'high_risk_second_emr_contact';
    const EMR_CONTACT_NUMBER_GROUP = 'emr_contact_number';
    const FULL_PAYMENT_AMOUNT_GROUP = 'full_payment_amount';
    const FULL_PAYMENT_DEADLINE_GROUP = 'full_payment_deadline';
    const FIRST_PAYMENT_DEADLINE_GROUP = 'first_payment_deadline';
    const PAYMENT_COMPLETE_EMAIL_GROUP = 'payment_complete_email';
    const PAYMENT_COMPLETE_EMAIL_SUBJECT_GROUP = 'payment_complete_email_subject';
    const PAYMENT_COMPLETE_EMAIL_CONTENT_GROUP = 'payment_complete_email_content';
    const PAYMENT_COMPLETE_EMAIL_REPLY_TO_GROUP = 'payment_complete_email_reply_to';

    private $visit;
    // Organiser Section
    public $organiser;
    public $organiserMobile;
    public $secondOrganiserMobile;
    // Main Section
    public $title;
    public $location;
    public $category;
    public $osaRequired = true;
    public $area;
    public $description;
    public $showOnCollegeCalendar = true;
    public $riskAssessments;
    // Participants Section
    public $proposedNumberOfStudents;
    public $minimumNumberOfStudents;
    public $maximumNumberOfStudents;
    public $proposedNumberOfStaff;
    // Date Section
    public $academicYear;
    public $startDate;
    public $endDate;
    public $startTime;
    public $endTime;
    public $recurrencePattern;
    // Emergency Contact Section
    public $emergencyContact;
    public $secondEmergencyContactName;
    public $secondEmergencyContactLandline;
    public $secondEmergencyContactMobile;
    // Payment Section
    public $paymentRequired = false;
    public $fullPaymentAmount = 0;
    public $fullPaymentDeadline;
    public $firstPaymentAmount = 0;
    public $firstPaymentDeadline;
    public $paymentCompleteEmail;
    public $replyToEmailAddress;
    public $emailSubject;
    public $emailContent;

    public function __construct(Visit $visit = null)
    {
        if (null === $this->visit = $visit) {
            return;
        }

        // Populate fields if visit exists
        // Organiser Section
        $this->organiser = $visit->getOrganiser();
        $this->organiserMobile = $visit->getOrganiserMobile();
        $this->secondOrganiserMobile = $visit->getOrganiserMobileSecond();
        // Main Section
        $this->title = $visit->getTitle();
        $this->location = $visit->getLocation();
        $this->category = $visit->getCategory();
        $this->osaRequired = $visit->isOsaRequired();
        $this->area = $visit->getArea();
        $this->description = $visit->getDescription();
        $this->showOnCollegeCalendar = $visit->isToBeShownOnCalendar();
        $this->riskAssessments = $visit->getRiskAssessments();
        // Participants Section
        $this->proposedNumberOfStudents = $visit->getProposedNoStudents();
        $this->minimumNumberOfStudents = $visit->getMinimumStudents();
        $this->maximumNumberOfStudents = $visit->getMaximumStudents();
        $this->proposedNumberOfStaff = $visit->getProposedNoStaff();
        // Date Section
        $this->academicYear = $visit->getAcademicYear();
        $this->startDate = $visit->getStartDate();
        $this->endDate = $visit->getEndDate();
        $this->startTime = $visit->getStartTime();
        $this->endTime = $visit->getEndTime();
        $this->recurrencePattern = $visit->getRecurrencePattern();
        // Emergency Contact Section
        $this->emergencyContact = $visit->getEmergencyContact();
        $this->secondEmergencyContactName = $visit->getSecondContactName();
        $this->secondEmergencyContactLandline = $visit->getSecondContactNumber();
        $this->secondEmergencyContactMobile = $visit->getSecondContactMobile();
        // Payment Section
        $this->paymentRequired = $visit->getFullPaymentAmount() > 0;
        $this->fullPaymentAmount = $visit->getFullPaymentAmount();
        $this->fullPaymentDeadline = $visit->getFullPaymentDeadline();
        $this->firstPaymentAmount = $visit->getFirstPaymentAmount();
        $this->firstPaymentDeadline = $visit->getFirstPaymentDeadline();
        $this->paymentCompleteEmail = $visit->getPaymentCompleteEmailReplyTo() !== null;
        $this->replyToEmailAddress = $visit->getPaymentCompleteEmailReplyTo();
        $this->emailSubject = $visit->getPaymentCompleteEmailSubject();
        $this->emailContent = $visit->getPaymentCompleteEmailContent();
    }

    public function getVisit()
    {
        return $this->visit;
    }

    public function setVisit(Visit $visit)
    {
        $this->visit = $visit;
        return $this;
    }

    public function getGroupSequence()
    {
        $groups = [self::DEFAULT_GROUP];

        $categories = [
            Visit::CATEGORY_OVERNIGHT_DAY_TRIP_HR,
            Visit::CATEGORY_OVERSEAS_HR
        ];

        if(in_array($this->category, $categories) and $this->secondEmergencyContactName === null) {
            $groups[] = self::HIGH_RISK_SECOND_EMR_CONTACT_GROUP;
        }

        if($this->secondEmergencyContactName !== null) {
            $landLine = $this->secondEmergencyContactLandline;
            $mobile = $this->secondEmergencyContactLandline;
            if(($landLine === '' or $landLine === null) and ($mobile === '' or $mobile === null)) {
                $groups[] = self::EMR_CONTACT_NUMBER_GROUP;
            }
        }

        if($this->paymentRequired and $this->fullPaymentAmount < 0.01) {
            $groups[] = self::FULL_PAYMENT_AMOUNT_GROUP;
        }

        if($this->fullPaymentAmount > 0 and $this->fullPaymentDeadline === null) {
            $groups[] = self::FULL_PAYMENT_DEADLINE_GROUP;
        }

        if($this->firstPaymentAmount > 0 and $this->firstPaymentDeadline === null) {
            $groups[] = self::FIRST_PAYMENT_DEADLINE_GROUP;
        }

        if($this->fullPaymentAmount < 1 and $this->paymentCompleteEmail === true) {
            $groups[] = self::PAYMENT_COMPLETE_EMAIL_GROUP;
        }

        if($this->paymentCompleteEmail === true and $this->fullPaymentAmount > 0) {
            if($this->emailSubject === null) {
                $groups[] = self::PAYMENT_COMPLETE_EMAIL_SUBJECT_GROUP;
            }
            if($this->emailContent === null) {
                $groups[] = self::PAYMENT_COMPLETE_EMAIL_CONTENT_GROUP;
            }
            if($this->replyToEmailAddress === null) {
                $groups[] = self::PAYMENT_COMPLETE_EMAIL_REPLY_TO_GROUP;
            }
        }

        return [$groups];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->setGroupSequenceProvider(true);

        $notBlanks = [
            'organiser',
            'title',
            'location',
            'category',
            'area',
            'description',
            'proposedNumberOfStudents',
            'minimumNumberOfStudents',
            'maximumNumberOfStudents',
            'proposedNumberOfStaff',
            'academicYear',
            'startDate',
            'startTime',
            'endTime',
            'emergencyContact'
        ];

        foreach ($notBlanks as $field) {
            $metadata->addPropertyConstraint($field, new Assert\NotBlank(['groups' => self::DEFAULT_GROUP]));
        }

        $lengths = [
            'title' => Visit::MAX_LENGTH_TITLE,
            'location' => Visit::MAX_LENGTH_LOCATION,
            'organiserMobile' => Visit::MAX_LENGTH_ORGANISER_MOBILE,
            'secondOrganiserMobile' => Visit::MAX_LENGTH_ORGANISER_MOBILE_SECOND,
            'secondEmergencyContactName' => Visit::MAX_LENGTH_SECOND_CONTACT_NAME,
            'secondEmergencyContactLandline' => Visit::MAX_LENGTH_SECOND_CONTACT_NUMBER,
            'secondEmergencyContactMobile' => Visit::MAX_LENGTH_SECOND_CONTACT_MOBILE,
            'emailSubject' => Visit::MAX_LENGTH_PAYMENT_COMPLETE_EMAIL_SUBJECT,
            'emailContent' => Visit::MAX_LENGTH_PAYMENT_COMPLETE_EMAIL_CONTENT,
            'replyToEmailAddress' => Visit::MAX_LENGTH_PAYMENT_COMPLETE_EMAIL_REPLY_TO,
        ];

        foreach ($lengths as $field => $length) {
            $metadata->addPropertyConstraint($field, new Assert\Length([
                'groups' => self::DEFAULT_GROUP,
                'max' => $length
            ]));
        }

        $metadata->addPropertyConstraint('replyToEmailAddress', new Assert\Email());
        $metadata->addPropertyConstraint('organiserMobile', new MobileNumber());
        $metadata->addPropertyConstraint('secondEmergencyContactLandline', new PhoneNumber());
        $metadata->addPropertyConstraint('secondEmergencyContactMobile', new MobileNumber());

        $metadata->addPropertyConstraint('secondEmergencyContactName', new Assert\NotBlank([
            'groups' => self::HIGH_RISK_SECOND_EMR_CONTACT_GROUP,
            'message' => 'High risk categories need an emergency contact.'
        ]));
        $metadata->addPropertyConstraint('secondEmergencyContactLandline', new Assert\NotBlank([
            'groups' => self::EMR_CONTACT_NUMBER_GROUP,
            'message' => 'Second emergency contact needs at least one number.'

        ]));
        $metadata->addPropertyConstraint('fullPaymentAmount', new Assert\GreaterThan([
            'value' => 0.01,
            'groups' => self::FULL_PAYMENT_AMOUNT_GROUP,
        ]));
        $metadata->addPropertyConstraint('fullPaymentDeadline', new Assert\NotBlank([
            'groups' => self::FULL_PAYMENT_DEADLINE_GROUP,
        ]));
        $metadata->addPropertyConstraint('firstPaymentDeadline', new Assert\NotBlank([
            'groups' => self::FIRST_PAYMENT_DEADLINE_GROUP,
        ]));
        $metadata->addPropertyConstraint('paymentCompleteEmail', new Assert\IsFalse([
            'groups' => self::PAYMENT_COMPLETE_EMAIL_GROUP,
            'message' => 'Missing payment amounts.'
        ]));
        $metadata->addPropertyConstraint('emailSubject', new Assert\NotBlank([
            'groups' => self::PAYMENT_COMPLETE_EMAIL_SUBJECT_GROUP,
        ]));
        $metadata->addPropertyConstraint('emailContent', new Assert\NotBlank([
            'groups' => self::PAYMENT_COMPLETE_EMAIL_CONTENT_GROUP,
        ]));
        $metadata->addPropertyConstraint('replyToEmailAddress', new Assert\NotBlank([
            'groups' => self::PAYMENT_COMPLETE_EMAIL_REPLY_TO_GROUP,
        ]));
    }
}
