<?php

namespace Cis\EducationalVisitBundle\Form\Participant;

use App\Form\Type\EmployeeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddStaffParticipantFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('employee' , EmployeeType::class, [
                'required' => true
            ])
        ;
    }
}