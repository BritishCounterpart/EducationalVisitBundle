<?php

namespace Cis\EducationalVisitBundle\Twig;

use App\Entity\Misc\GeneralCode;
use App\Entity\PersonalRecord\Note;
use App\Entity\PersonalRecord\Option;
use App\Entity\Student\DifficultyDisabilityRecord;
use App\Entity\Student\HealthWellbeingRecord;
use App\Entity\Student\Student;
use App\Util\GeneralCodeUtil;
use App\Util\PersonalRecordUtil;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Petroc\Component\Helper\Orm;
use Twig_Extension;
use Twig_SimpleFunction;

class CisEducationalVisitExtension extends Twig_Extension
{
    private $orm;
    private $generalCodeUtil;
    private $participantUtil;
    private $personalRecordUtil;
    private $osaConsent = [];

    public function __construct(Orm $orm, GeneralCodeUtil $generalCodeUtil, ParticipantUtil $participantUtil, PersonalRecordUtil $personalRecordUtil)
    {
        $this->orm = $orm;
        $this->generalCodeUtil = $generalCodeUtil;
        $this->participantUtil = $participantUtil;
        $this->personalRecordUtil = $personalRecordUtil;
    }

    public function getFunctions()
    {
        $defaultOptions = ['is_safe' => ['html']];

        $functionList = [
            'cis_educational_visit_participant_can_go' => 'participantCanGo',
            'cis_educational_visit_participant_confirmed' => 'participantConfirmed',
            'cis_educational_visit_participant_missing_osa_consent' => 'participantMissingOsaConsent',
            'cis_educational_visit_participant_health_and_welling' => 'participantHealthAndWelling',
            'cis_educational_visit_participant_difficulties_and_disabilities' => 'participantDifficultiesAndDisabilities',
            'cis_educational_visit_participant_dietary_requirements' => 'participantDietaryRequirements',
            'cis_educational_visit_participant_medication' => 'participantMedication',
            'cis_educational_visit_participant_alcohol_consent' => 'participantAlcoholConsent'
        ];

        $functions = [];

        foreach ($functionList as $twigValue => $methodName) {
            $functions[] = new Twig_SimpleFunction($twigValue, [$this, $methodName], $defaultOptions);
        }

        return $functions;
    }

    public function participantCanGo(StudentParticipant $studentParticipant, array $missingOsaConsent, array $payment)
    {
        return $this->participantUtil->isAbleToGo($studentParticipant, $missingOsaConsent, $payment);
    }

    public function participantConfirmed(array $payment)
    {
        return $this->participantUtil->hasConfirmed($payment);
    }

    public function participantMissingOsaConsent(StudentParticipant $studentParticipant)
    {
        return $this->participantUtil->getMissingOSAConsent($studentParticipant);
    }

    public function participantHealthAndWelling(StudentParticipant $studentParticipant)
    {
        $records = $this->orm->getRepository(HealthWellbeingRecord::class)->findByStudent($studentParticipant->getStudent());

        $descriptions = [];

        foreach($records as $record) {
            $descriptions[] = $this->generalCodeUtil->getDescription($record->getCode(), GeneralCode::CATEGORY_STUDENT_HEALTH_WELLBEING);
        }

        return $descriptions;
    }

    public function participantDifficultiesAndDisabilities(StudentParticipant $studentParticipant)
    {
        $academicYear = $studentParticipant->getVisit()->getAcademicYear();
        $records = $this->orm->getRepository(DifficultyDisabilityRecord::class)->findByStudentAndAcademicYear($studentParticipant->getStudent(), $academicYear);

        $descriptions = [];

        foreach($records as $record) {
            $descriptions[] = $this->generalCodeUtil->getDescription($record->getCode(), GeneralCode::CATEGORY_STUDENT_DIFFICULTY_DISABILITY);
        }

        return $descriptions;
    }

    public function participantDietaryRequirements(StudentParticipant $studentParticipant)
    {
        $osaConsent = $this->getOsaConsent($studentParticipant->getStudent());
        return $this->personalRecordUtil->getOptionDataValue($osaConsent, Option::OSA_CONSENT_DIETARY_REQUIREMENTS_ID);
    }

    public function participantMedication(StudentParticipant $studentParticipant)
    {
        $osaConsent = $this->getOsaConsent($studentParticipant->getStudent());
        return $this->personalRecordUtil->getOptionDataValue($osaConsent, Option::OSA_CONSENT_MEDICATION_ID);
    }

    public function participantAlcoholConsent(StudentParticipant $studentParticipant)
    {
        $osaConsent = $this->getOsaConsent($studentParticipant->getStudent());
        return $this->personalRecordUtil->getOptionDataValue($osaConsent, Option::OSA_CONSENT_ALCOHOL_ID);
    }

    private function getOsaConsent(Student $student)
    {
        $id = $student->getId();
        if(array_key_exists($id, $this->osaConsent)) {
            return $this->osaConsent[$id];
        }

        $osaConsent = $this->orm
            ->getRepository(Note::class)
            ->findOneOsaConsentByStudent(
                $student
            );

        // Cache to prevent being re-queried
        return $this->osaConsent[$id] = $osaConsent;
    }

    public function getName()
    {
        return 'cis_educational_visit_extension';
    }
}