<?php

namespace Cis\EducationalVisitBundle\Form\Detail;

use App\Form\Type\AcademicYearType;
use App\Form\Type\DayType;
use App\Form\Type\EmployeeType;
use App\Form\Type\PhoneNumberType;
use App\Form\Type\TimeType;
use Cis\EducationalVisitBundle\Form\Type\AreaType;
use Cis\EducationalVisitBundle\Form\Type\CategoryType;
use Cis\EducationalVisitBundle\Form\Type\RiskAssessmentType;
use Petroc\CoreBundle\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Organiser Section
            ->add('organiser', EmployeeType::class, [
                'required' => true
            ])
            ->add('organiserMobile', PhoneNumberType::class, [
                'required' => false,
                'label' => 'Mobile'
            ])
            ->add('secondOrganiserMobile', PhoneNumberType::class, [
                'required' => false,
                'label' => '2nd mobile'
            ])
            // Main Section
            ->add('title', TextType::class, [
                'required' => true
            ])
            ->add('location', TextType::class, [
                'required' => true
            ])
            ->add('category', CategoryType::class, [
                'placeholder' => 'Please select.',
                'required' => true
            ])
            ->add('osaRequired', ToggleType::class, [
                'required' => false,
                'label' => 'OSA Consent required'
            ])
            ->add('area', AreaType::class, [
                'required' => true
            ]) 
            ->add('description', TextareaType::class, [
                'required' => true
            ])
            ->add('showOnCollegeCalendar', ToggleType::class, [
                'required' => true
            ])
            ->add('riskAssessments', CollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
                'entry_type' => RiskAssessmentType::class
            ])
            // Participants Section 
            ->add('proposedNumberOfStudents', NumberType::class, [
                'required' => true
            ])  
            ->add('minimumNumberOfStudents', NumberType::class, [
                'required' => true
            ])  
            ->add('maximumNumberOfStudents', NumberType::class, [
                'required' => true
            ])
            ->add('proposedNumberOfStaff', NumberType::class, [
                'required' => true
            ])   
            // Date Section
            ->add('academicYear', AcademicYearType::class, [
                'required' => true
            ])
            ->add('startDate', DateType::class, [
                'required' => true
            ])
            ->add('endDate', DateType::class, [
                'required' => false
            ])
            ->add('startTime', TimeType::class, [
                'required' => true
            ])
            ->add('endTime', TimeType::class, [
                'required' => true
            ])
            ->add('recurrencePattern', DayType::class, [
                'required' => false,
                'multiple' => false,
                'placeholder' => 'N/A',
                'help_text' => 'Select a day if the visit is a recurring visit, each week.'
            ])
            // Emergency Contact Section 
            ->add('emergencyContact', EmployeeType::class, [
                'required' => true
            ])
            ->add('secondEmergencyContactName', TextType::class, [
                'required' => false
            ]) 
            ->add('secondEmergencyContactLandline', PhoneNumberType::class, [
                'required' => false
            ]) 
            ->add('secondEmergencyContactMobile', PhoneNumberType::class, [
                'required' => false
            ]) 
            // Payment Section
            ->add('paymentRequired', ToggleType::class, [
                'required' => true,
            ])
            ->add('fullPaymentAmount', MoneyType::class, [
               'required' => true,
            ]) 
            ->add('fullPaymentDeadline', DateType::class, [
               'required' => false,
            ])
            ->add('firstPaymentAmount', MoneyType::class, [
               'required' => true,
            ]) 
            ->add('firstPaymentDeadline', DateType::class, [
               'required' => false,
            ])
            ->add('paymentCompleteEmail', ToggleType::class, [
               'required' => true,
            ])
            ->add('replyToEmailAddress', EmailType::class, [
               'required' => false,
            ])
            ->add('emailSubject', TextType::class, [
               'required' => false,
            ])
            ->add('emailContent', TextareaType::class, [
               'required' => false,
            ])
        ;
    }
}
