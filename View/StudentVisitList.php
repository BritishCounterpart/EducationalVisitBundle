<?php

namespace Cis\EducationalVisitBundle\View;

use App\Entity\Student\Student;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Petroc\Component\View\TraversableOrmData;

class StudentVisitList extends TraversableOrmData
{
    private $util;
    private $student;

    public function __construct(ParticipantUtil $util, Student $student)
    {
        $this->util = $util;
        $this->student = $student;
    }

    public function getItems()
    {
        $studentParticipants = $this->getRepository(StudentParticipant::class)->findByStudent($this->student);

        $list = [];

        foreach($studentParticipants as $studentParticipant) {
            $visit = $studentParticipant->getVisit();
            if($studentParticipant->isNoLongerGoing() === false and $visit->getStatus() === Visit::STATUS_APPROVED) {
                $payment = $this->util->getStudentPayment($studentParticipant);
                $list[] = [
                    'studentParticipant' => $studentParticipant,
                    'payment' => $payment
                ];
            }
        }

        return $list;

    }

    public function getLegacyStudent()
    {
        $idNumber = $this->student->getIdNumber();

        $sql = "SELECT * FROM OPENQUERY(QRCS11_MISDEV,'SELECT OBJECT_ID FROM PERSON WHERE ID_NUMBER = $idNumber') Q";
        $stmt = $this->getOrm()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}