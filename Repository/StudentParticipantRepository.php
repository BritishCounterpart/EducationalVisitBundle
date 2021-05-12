<?php

namespace Cis\EducationalVisitBundle\Repository;

use App\Entity\Cohort\Cohort;
use App\Entity\Student\Student;
use Cis\EducationalVisitBundle\Entity\Visit;
use Doctrine\ORM\EntityRepository;

class StudentParticipantRepository extends EntityRepository
{
    protected function createDefaultQueryBuilder(Visit $visit)
    {
        return $this
            ->createQueryBuilder('p')
            ->addSelect('s')
            ->addSelect('v')
            ->join('p.student', 's')
            ->join('p.visit', 'v')
            ->where('v.id = :visit')
            ->setParameter('visit', $visit->getId())
            ->orderBy('s.surname')
            ->addOrderBy('s.firstName')
        ;
    }

    public function findByVisit(Visit $visit)
    {
        return $this
            ->createDefaultQueryBuilder($visit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findStudentsByVisit(Visit $visit)
    {
        $students = [];

        $participants = $this
            ->createDefaultQueryBuilder($visit)
            ->getQuery()
            ->getResult()
        ;

        foreach($participants as $participant) {
            $students[$participant->getId()] = $participant->getStudent();
        }

        return $students;
    }


    public function findByVisitAndCohort(Visit $visit, Cohort $cohort)
    {
        return $this
            ->createDefaultQueryBuilder($visit)
            ->addSelect('c')
            ->join('p.cohort', 'c')
            ->andWhere('c.id = :cohort')
            ->setParameter('cohort', $cohort->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByStudentAndVisit(Student $student, Visit $visit)
    {
        return $this
            ->createDefaultQueryBuilder($visit)
            ->andWhere('s.id = :student')
            ->setParameter('student', $student->getId())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByStudent(Student $student)
    {
        return $this
            ->createQueryBuilder('p')
            ->addSelect('s')
            ->addSelect('v')
            ->join('p.student', 's')
            ->join('p.visit', 'v')
            ->andWhere('s.id = :student')
            ->setParameter('student', $student->getId())
            ->orderBy('v.startDate', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}