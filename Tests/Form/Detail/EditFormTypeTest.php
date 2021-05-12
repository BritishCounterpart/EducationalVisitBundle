<?php

namespace Cis\EducationalVisitBundle\Tests\Form\Detail;

use App\Entity\Employee\Employee;
use App\Entity\Misc\GeneralCode;
use App\Entity\Misc\RiskAssessment;
use App\Entity\User;
use App\Tests\EmptyEntityTableFixture;
use App\Tests\FormTypeTestCase;
use Cis\EducationalVisitBundle\Entity\Area;
use Cis\EducationalVisitBundle\Form\Detail\EditFormType;

class EditFormTypeTest extends FormTypeTestCase
{
    public function testCompile()
    {
        $form = $this->factory->create(EditFormType::class);
        $view = $form->createView();
        // Organiser Section
        $this->assertArrayHasKey('organiser', $view);
        $this->assertArrayHasKey('organiserMobile', $view);
        $this->assertArrayHasKey('secondOrganiserMobile', $view);
        // Main Section
        $this->assertArrayHasKey('title', $view);
        $this->assertArrayHasKey('location', $view);
        $this->assertArrayHasKey('category', $view);
        $this->assertArrayHasKey('osaRequired', $view);
        $this->assertArrayHasKey('area', $view);
        $this->assertArrayHasKey('description', $view);
        $this->assertArrayHasKey('showOnCollegeCalendar', $view);
        $this->assertArrayHasKey('riskAssessments', $view);
        // Participants Section
        $this->assertArrayHasKey('proposedNumberOfStudents', $view);
        $this->assertArrayHasKey('minimumNumberOfStudents', $view);
        $this->assertArrayHasKey('maximumNumberOfStudents', $view);
        $this->assertArrayHasKey('proposedNumberOfStaff', $view);
        // Date Section
        $this->assertArrayHasKey('academicYear', $view);
        $this->assertArrayHasKey('startDate', $view);
        $this->assertArrayHasKey('endDate', $view);
        $this->assertArrayHasKey('startTime', $view);
        $this->assertArrayHasKey('endTime', $view);
        $this->assertArrayHasKey('recurrencePattern', $view);
        // Emergency Contact Section
        $this->assertArrayHasKey('emergencyContact', $view);
        $this->assertArrayHasKey('secondEmergencyContactName', $view);
        $this->assertArrayHasKey('secondEmergencyContactLandline', $view);
        $this->assertArrayHasKey('secondEmergencyContactMobile', $view);
        // Payment Section
        $this->assertArrayHasKey('paymentRequired', $view);
        $this->assertArrayHasKey('fullPaymentAmount', $view);
        $this->assertArrayHasKey('fullPaymentDeadline', $view);
        $this->assertArrayHasKey('firstPaymentAmount', $view);
        $this->assertArrayHasKey('firstPaymentDeadline', $view);
        $this->assertArrayHasKey('paymentCompleteEmail', $view);
        $this->assertArrayHasKey('replyToEmailAddress', $view);
        $this->assertArrayHasKey('emailSubject', $view);
        $this->assertArrayHasKey('emailContent', $view);
        $this->assertCount(34, $view);
    }

    protected function getTableFixtures()
    {
        return [
            new EmptyEntityTableFixture(Area::class),
            new EmptyEntityTableFixture(Employee::class),
            new EmptyEntityTableFixture(GeneralCode::class),
            new EmptyEntityTableFixture(User::class),
            new EmptyEntityTableFixture(RiskAssessment::class)
        ];
    }
}