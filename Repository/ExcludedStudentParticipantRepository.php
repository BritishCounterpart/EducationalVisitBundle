<?php

namespace Cis\EducationalVisitBundle\Repository;

use App\Entity\Student\Student;
use Cis\EducationalVisitBundle\Entity\Visit;
use Doctrine\ORM\EntityRepository;

class ExcludedStudentParticipantRepository extends EntityRepository
{
    public function findByVisit(Visit $visit)
    {
        return $this
            ->createQueryBuilder('ep')
            ->addSelect('s')
            ->addSelect('v')
            ->join('ep.student', 's')
            ->join('ep.visit', 'v')
            ->where('v.id = :visit')
            ->setParameter('visit', $visit->getId())
            ->orderBy('s.surname')
            ->addOrderBy('s.firstName')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findStudentsByVisit(Visit $visit)
    {
        $excludedStudentParticipants = $this->findByVisit($visit);

        $excludedStudents = [];

        foreach($excludedStudentParticipants as $excludedStudentParticipant) {
            $excludedStudents[] = $excludedStudentParticipant->getStudent();
        }

        return $excludedStudents;
    }

    public function findOneByStudentAndVisit(Student $student, Visit $visit)
    {
        return $this
            ->createQueryBuilder('ep')
            ->addSelect('s')
            ->addSelect('v')
            ->join('ep.student', 's')
            ->join('ep.visit', 'v')
            ->where('v.id = :visit')
            ->setParameter('visit', $visit->getId())
            ->andWhere('s.id = :student')
            ->setParameter('student', $student->getId())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}