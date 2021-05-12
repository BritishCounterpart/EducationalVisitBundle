<?php

namespace Cis\EducationalVisitBundle\Form\Payment;

use Cis\EducationalVisitBundle\Form\Type\PaymentStatusType;
use Petroc\CoreBundle\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('paymentStatus' , PaymentStatusType::class, [
                'required' => false
            ])
            ->add('showNoLongerGoing', ToggleType::class, [
                'required' => false
            ])
        ;
    }
}