<?php

namespace Cis\EducationalVisitBundle\Form\Payment;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangeStudentParticipantPaymentAmountFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullPaymentAmount', MoneyType::class, [
                'required' => false,
            ])
            ->add('firstPaymentAmount', MoneyType::class, [
                'required' => false,
            ])
        ;
    }
}