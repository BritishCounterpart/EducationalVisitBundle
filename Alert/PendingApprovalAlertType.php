<?php

namespace Cis\EducationalVisitBundle\Alert;

use App\Alert\AlertBuilder;
use App\Alert\AlertTypeInterface;
use Cis\EducationalVisitBundle\Entity\Visit;

class PendingApprovalAlertType implements AlertTypeInterface
{
    public function buildAlert(AlertBuilder $builder)
    {
        if (!$builder->isStaff()) {
            return;
        }

        $visits = $builder
            ->getRepository(Visit::class)
            ->findPendingApprovalVisitByApprover($builder->getUser())
        ;

        $visits = count($visits);

        if($visits < 1) {
            return;
        }

        $builder->add(
            sprintf('Educational Visits Pending Approval'),
            $builder->generatePath('cis_educational_visit'),
            1,
            $visits
        );
    }
}