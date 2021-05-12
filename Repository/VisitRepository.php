<?php

namespace Cis\EducationalVisitBundle\Repository;

use App\Entity\Employee\Employee;
use App\Entity\User;
use Cis\EducationalVisitBundle\Entity\AreaApprover;
use Cis\EducationalVisitBundle\Entity\Visit;
use Doctrine\ORM\EntityRepository;
use DateTime;
use DateInterval;
use Doctrine\ORM\Query\Expr\Join;

class VisitRepository extends EntityRepository
{
    protected function createDefaultQueryBuilder()
    {
        return $this
            ->createQueryBuilder('v')
            ->addSelect('i')
            ->addSelect('it')
            ->addSelect('o')
            ->leftJoin('v.income', 'i')
            ->leftJoin('v.itinerary', 'it')
            ->leftJoin('v.organiser', 'o')
        ;
    }

    public function match(VisitCriteria $criteria)
    {
        $qb = $this
            ->createDefaultQueryBuilder()
            ->where('v.academicYear = :academic_year')
            ->setParameter('academic_year', $criteria->academicYear)
        ;

        if(null !== $keyword = $criteria->keyword) {
            $qb
                ->andWhere('(LOWER(v.title) LIKE :keyword OR LOWER(v.description) LIKE :keyword OR v.evNumber like :keyword)')
                ->setParameter('keyword', '%'.str_replace(' ','%',strtolower($keyword)).'%');
        }

        if(null !== $category = $criteria->category) {
            $qb
                ->andWhere('v.category = :category')
                ->setParameter('category', $category);
        }

        if(null !== $area = $criteria->area) {
            $qb
                ->addSelect('a')
                ->join('v.area', 'a')
                ->andWhere('a.id = :area')
                ->setParameter('area', $area->getId());
        }

        if(null !== $organiser = $criteria->organiser) {
            $qb
                ->andWhere('o.id = :organiser')
                ->setParameter('organiser', $organiser->getId());
        }

        if(null !== $orderBy = $criteria->orderBy) {
            switch ($orderBy) {
                case 'Date':
                    $qb
                        ->orderBy('v.startDate', 'DESC')
                        ->addOrderBy('v.startTime', 'DESC')
                    ;
                    break;
                case 'EV Number':
                    $qb->orderBy('v.evNumber');
                    break;
                case 'Educational Visit':
                    $qb->orderBy('v.title');
                    break;
                case 'Organiser':
                    $qb
                        ->orderBy('o.surname')
                        ->addOrderBy('o.firstName')
                    ;
                    break;
            }
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function findUpcomingByEmployeeAndAreas(Employee $employee, array $Areas)
    {
        // 90 day upcoming period
        $date = new DateTime();
        $date->add(new DateInterval('P90D'));

        $ids = [];

        foreach($Areas as $area) {
            $ids[] = $area->getId();
        }

        return $this
            ->createDefaultQueryBuilder()
            ->addSelect('a')
            ->join('v.area', 'a')
            ->where('(o.id = :employee OR a.id IN (:areas))')
            ->setParameter('employee', $employee->getId())
            ->setParameter('areas', $ids)
            ->andWhere('v.startDate <= :date')
            ->setParameter('date', $date)
            ->andWhere('v.startDate >= CURRENT_DATE()')
            ->orderBy('v.startDate', 'ASC')
            ->addOrderBy('v.endDate', 'ASC')
            ->addOrderBy('v.title')
            ->getQuery()
            ->getResult()
        ;
    }


    public function findIssuesByEmployeeAndAreas(Employee $employee, array $areas)
    {
        $ids = [];

        foreach($areas as $area) {
            $ids[] = $area->getId();
        }

        return $this
            ->createDefaultQueryBuilder()
            ->addSelect('a')
            ->join('v.area', 'a')
            ->where('(o.id = :employee OR a.id IN (:areas))')
            ->setParameter('employee', $employee->getId())
            ->setParameter('areas', $ids)
            ->andWhere('v.issues is not null')
            ->orderBy('v.startDate', 'ASC')
            ->addOrderBy('v.endDate', 'ASC')
            ->addOrderBy('v.title')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPendingApprovalVisitByApprover(User $user)
    {
        $em = $this->getEntityManager();
        $expr = $em->getExpressionBuilder();

        return $this
            ->createDefaultQueryBuilder()
            ->addSelect('a')
            ->join('v.area', 'a')
            ->where('v.status = :status')
            ->setParameter('status', Visit::STATUS_PENDING_APPROVAL)
            ->andWhere(
                $expr->in(
                    'a.id',
                    $em->createQueryBuilder()
                        ->select('a2.id')
                        ->from(AreaApprover::class, 'aa')
                        ->join('aa.area', 'a2')
                        ->join('aa.user', 'u')
                        ->andWhere('u.id = :user')
                    ->getDQL()
                )
            )
            ->setParameter('user', $user->getId())
            ->orderBy('v.startDate', 'ASC')
            ->addOrderBy('v.endDate', 'ASC')
            ->addOrderBy('v.title')
            ->getQuery()
            ->getResult()
        ;
    }
}