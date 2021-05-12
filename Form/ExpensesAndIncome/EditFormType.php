<?php

namespace Cis\EducationalVisitBundle\Form\ExpensesAndIncome;

use Cis\EducationalVisitBundle\Form\Type\ExpenseType;
use Petroc\CoreBundle\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Cost Section
            ->add('anyExpenses' , ToggleType::class, [
                'required' => true
            ])
            ->add('expenses', CollectionType::class, [
                'entry_type' => ExpenseType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false
            ])
            // Income Section
            ->add('anyIncome' , ToggleType::class, [
                'required' => true
            ])
            ->add('studentsPays', MoneyType::class, [
                'label' => 'Students pays (Total)',
                'required' => false
            ])
            ->add('collegePays', MoneyType::class, [
                'label' => 'College pays (Total)',
                'required' => false
            ])
            ->add('otherPayInfo', TextType::class, [
                'required' => false
            ])
            ->add('otherPay', MoneyType::class, [
                'label' => 'Other pays (Total)',
                'required' => false
            ])
        ;
    }
}