<?php

namespace Cis\EducationalVisitBundle\Form\Type;

use Cis\EducationalVisitBundle\View\AbstractFilterableStudentParticipantList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentStatusType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = [
            AbstractFilterableStudentParticipantList::PAYMENT_STATUS_PAID,
            AbstractFilterableStudentParticipantList::PAYMENT_STATUS_PARTIAL_PAYMENT,
            AbstractFilterableStudentParticipantList::PAYMENT_STATUS_REFUNDED,
            AbstractFilterableStudentParticipantList::PAYMENT_STATUS_PARTIAL_REFUND,
            AbstractFilterableStudentParticipantList::PAYMENT_STATUS_NOT_PAID
        ];

        $resolver->setDefaults([
            'choices' => array_combine($choices,$choices),
            'placeholder' => 'All'
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}