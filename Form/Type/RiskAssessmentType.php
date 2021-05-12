<?php

namespace Cis\EducationalVisitBundle\Form\Type;

use App\Entity\Misc\RiskAssessment;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RiskAssessmentType extends AbstractType
{
    const GENERIC_RA = [
        609688558, // TRIP A Various local locations
        609689081, // TRIP B Any location in UK
        613930863 // TRIP C Any non UK country
    ];

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'placeholder' => false,
            'class' => RiskAssessment::class,
            'query_builder' => function (Options $options) {
                return function (EntityRepository $er) use ($options) {
                    return $er
                        ->createQueryBuilder('r')
                        ->select('r')
                        ->addSelect('(CASE 
                            WHEN r.id = 609688558 THEN 1
                            WHEN r.id = 609689081 THEN 2
                            WHEN r.id = 613930863 THEN 3
                            ELSE 4 END
                        ) AS HIDDEN ord')
                        ->where('r.status = :status')
                        ->setParameter('status',RiskAssessment::STATUS_CONFIRMED)
                        ->orderBy('ord')
                        ->addOrderBy('r.assessmentFor')
                    ;
                };
            },
            'group_by' => function (RiskAssessment $riskAssessment) {
                return in_array($riskAssessment->getId(), self::GENERIC_RA) ? 'Generic' : 'Other';
            },
            'choice_label' => function (RiskAssessment $riskAssessment) {
                return
                    in_array($riskAssessment->getId(), self::GENERIC_RA) ?
                        $riskAssessment->getId().' - '.$riskAssessment->getLocation().' - '.$riskAssessment->getAssessmentFor()
                    :
                        $riskAssessment;
            }
        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }
}