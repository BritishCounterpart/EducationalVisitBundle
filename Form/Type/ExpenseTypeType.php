<?php

namespace Cis\EducationalVisitBundle\Form\Type;

use App\Entity\Misc\GeneralCode;
use App\Form\Type\GeneralCodeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpenseTypeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'placeholder' => 'All',
            'required' => false,
            'category' => GeneralCode::CATEGORY_EDUCATIONAL_VISIT_EXPENSE
        ]);
    }

    public function getParent()
    {
        return GeneralCodeType::class;
    }
}
