<?php

namespace Cis\EducationalVisitBundle\Repository;

use App\Entity\Employee\Employee;
use Cis\EducationalVisitBundle\Entity\Visit;
use Doctrine\ORM\EntityRepository;

class StaffParticipantRepository extends EntityRepository
{
    public function findByVisit(Visit $visit)
    {
        return $this
            ->createQueryBuilder('p')
            ->addSelect('e')
            ->addSelect('v')
            ->join('p.employee', 'e')
            ->join('p.visit', 'v')
            ->where('v.id = :visit')
            ->setParameter('visit', $visit->getId())
            ->orderBy('e.surname')
            ->addOrderBy('e.firstName')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByEmployeeAndVisit(Employee $employee, Visit $visit)
    {
        return $this
            ->createQueryBuilder('p')
            ->addSelect('e')
            ->addSelect('v')
            ->join('p.employee', 'e')
            ->join('p.visit', 'v')
            ->where('v.id = :visit')
            ->setParameter('visit', $visit->getId())
            ->andWhere('e.id = :employee')
            ->setParameter('employee', $employee->getId())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}