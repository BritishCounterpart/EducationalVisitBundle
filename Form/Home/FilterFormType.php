<?php

namespace Cis\EducationalVisitBundle\Form\Home;

use App\Form\Type\AcademicYearType;
use App\Form\Type\EmployeeType;
use Cis\EducationalVisitBundle\Form\Type\CategoryType;
use Cis\EducationalVisitBundle\Form\Type\AreaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [
            'Dates',
            'EV Number',
            'Educational Visit',
            'Organiser',
        ];

        $builder
            ->add('academicYear' , AcademicYearType::class, [
                'required' => true
            ])
            ->add('keyword' , TextType::class, [
                'required' => false
            ])
            ->add('category' , CategoryType::class, [
                'required' => false
            ])
            ->add('area', AreaType::class, [
                'required' => false
            ])
            ->add('organiser', EmployeeType::class, [
                'placeholder' => 'All',
                'required' => false
            ])
            ->add('orderBy', ChoiceType::class, [
                'placeholder' => false,
                'required' => true,
                'choices' => array_combine($choices, $choices)
            ])
        ;
    }
}