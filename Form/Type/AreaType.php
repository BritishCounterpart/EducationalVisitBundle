<?php

namespace Cis\EducationalVisitBundle\Form\Type;

use Cis\EducationalVisitBundle\Entity\Area;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AreaType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'placeholder' => 'All',
            'class' => Area::class,
            'query_builder' => function (Options $options) {
                return function (EntityRepository $er) use ($options) {
                    return $er
                        ->createQueryBuilder('a')
                        ->select('a')
                        ->orderBy('a.name')
                    ;
                };
            }
        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
