<?php

namespace Cis\EducationalVisitBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ExpenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ExpenseTypeType::class, [
                'placeholder' => 'Please select.',
                'required' => true
            ])
            ->add('description', TextType::class, [
                'required' => true
            ])
            ->add('amount', MoneyType::class, [
                'required' => true
            ])
        ;
    }
}
