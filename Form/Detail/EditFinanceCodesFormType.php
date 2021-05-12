<?php

namespace Cis\EducationalVisitBundle\Form\Detail;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EditFinanceCodesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('evNumber', TextType::class, [
                'label' => 'EV Number'
            ])
            ->add('costCode', TextType::class)
        ;
    }

}