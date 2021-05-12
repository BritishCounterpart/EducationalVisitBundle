<?php

namespace Cis\EducationalVisitBundle\Tests\Twig;

use App\Entity\Misc\GeneralCode;
use App\Entity\PersonalRecord\Note;
use App\Entity\PersonalRecord\Option;
use App\Entity\Student\DifficultyDisabilityRecord;
use App\Entity\Student\HealthWellbeingRecord;
use App\Entity\Student\Student;
use App\Repository\PersonalRecord\NoteRepository;
use App\Repository\Student\DifficultyDisabilityRecordRepository;
use App\Repository\Student\HealthWellbeingRecordRepository;
use App\Util\GeneralCodeUtil;
use App\Util\PersonalRecordUtil;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Twig\CisEducationalVisitExtension;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\Orm;
use Prophecy\Argument;

class CisEducationalVisitExtensionTest extends TestCase
{
    private $orm;
    private $generalCodeUtil;
    private $participantUtil;
    private $personalRecordUtil;
    private $academicYear = 2020;
    private $visit;
    private $student;
    private $studentParticipant;


    protected function setUp()
    {
        $this->orm = $this->prophesize(Orm::class);
        $this->generalCodeUtil = $this->prophesize(GeneralCodeUtil::class);
        $this->participantUtil = $this->prophesize(ParticipantUtil::class);
        $this->personalRecordUtil = $this->prophesize(PersonalRecordUtil::class);
        $student = $this->prophesize(Student::class);
        $student->getId()->willReturn(567);
        $this->student = $student->reveal();
        $visit = $this->prophesize(Visit::class);
        $visit->getAcademicYear()->willReturn($this->academicYear);
        $this->visit = $visit->reveal();
        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getStudent()->willReturn($this->student);
        $studentParticipant->getVisit()->willReturn($this->visit);
        $this->studentParticipant = $studentParticipant->reveal();
    }

    private function createExtension()
    {
        return new CisEducationalVisitExtension(
            $this->orm->reveal(),
            $this->generalCodeUtil->reveal(),
            $this->participantUtil->reveal(),
            $this->personalRecordUtil->reveal()
        );
    }

    public function testGetFunctions()
    {
        $extension = $this->createExtension();
        $functions = $extension->getFunctions();

        $expected = [
            'cis_educational_visit_participant_can_go',
            'cis_educational_visit_participant_confirmed',
            'cis_educational_visit_participant_missing_osa_consent',
            'cis_educational_visit_participant_health_and_welling',
            'cis_educational_visit_participant_difficulties_and_disabilities',
            'cis_educational_visit_participant_dietary_requirements',
            'cis_educational_visit_participant_medication',
            'cis_educational_visit_participant_alcohol_consent'
        ];

        foreach ($functions as $function) {
            $this->assertContains($function->getName(), $expected);
        }

        $this->assertCount(count($expected), $functions);
    }

    public function testParticipantCanGo()
    {
        $studentParticipant = $this->studentParticipant;
        $missingOsaConsent = [];
        $payment = ['Paid'];

        $this->participantUtil
            ->isAbleToGo(
                Argument::exact($studentParticipant),
                Argument::exact($missingOsaConsent),
                Argument::exact($payment)
            )
            ->shouldBeCalledOnce()->willReturn(true);

        $extension = $this->createExtension();
        $participantCanGo = $extension->participantCanGo($studentParticipant, $missingOsaConsent, $payment);

        $this->assertTrue($participantCanGo);
    }

    public function testParticipantConfirmed()
    {
        $payment = ['Paid'];

        $this->participantUtil
            ->hasConfirmed(
                Argument::exact($payment)
            )
            ->shouldBeCalledOnce()->willReturn(true);

        $extension = $this->createExtension();
        $participantConfirmed = $extension->participantConfirmed($payment);

        $this->assertTrue($participantConfirmed);
    }

    public function testParticipantMissingOsaConsent()
    {
        $studentParticipant = $this->studentParticipant;
        $missingOsaConsent = ['Missing Student'];

        $this->participantUtil
            ->getMissingOSAConsent(
                Argument::exact($studentParticipant)
            )
            ->shouldBeCalledOnce()->willReturn($missingOsaConsent);

        $extension = $this->createExtension();
        $participantMissingOsaConsent = $extension->participantMissingOsaConsent($studentParticipant);

        $this->assertSame($missingOsaConsent, $participantMissingOsaConsent);
    }

    public function testParticipantHealthAndWelling()
    {
        $student = $this->student;
        $studentParticipant = $this->studentParticipant;

        $recordOne = $this->prophesize(HealthWellbeingRecord::class);
        $recordOne->getCode()->willReturn('HWCODE1');
        $recordOne = $recordOne->reveal();

        $recordTwo = $this->prophesize(HealthWellbeingRecord::class);
        $recordTwo->getCode()->willReturn('HWCODE2');
        $recordTwo = $recordTwo->reveal();

        $records = [
            $recordOne,
            $recordTwo
        ];

        $repo = $this->prophesize(HealthWellbeingRecordRepository::class);
        $repo->findByStudent(Argument::exact($student))->willReturn($records);
        $this->orm->getRepository(HealthWellbeingRecord::class)->willReturn($repo->reveal());

        $this->generalCodeUtil
            ->getDescription(
                Argument::exact('HWCODE1'),
                Argument::exact(GeneralCode::CATEGORY_STUDENT_HEALTH_WELLBEING)
            )
            ->shouldBeCalled()->willReturn('HW Code 1');

        $this->generalCodeUtil
            ->getDescription(
                Argument::exact('HWCODE2'),
                Argument::exact(GeneralCode::CATEGORY_STUDENT_HEALTH_WELLBEING)
            )
            ->shouldBeCalled()->willReturn('HW Code 2');

        $extension = $this->createExtension();
        $descriptions = $extension->participantHealthAndWelling($studentParticipant);

        $this->assertCount(2, $descriptions);
        $this->assertContains('HW Code 1', $descriptions);
        $this->assertContains('HW Code 2', $descriptions);
    }

    public function testParticipantDifficultiesAndDisabilities()
    {
        $student = $this->student;
        $studentParticipant = $this->studentParticipant;

        $recordOne = $this->prophesize(DifficultyDisabilityRecord::class);
        $recordOne->getCode()->willReturn('DDCODE1');
        $recordOne = $recordOne->reveal();

        $recordTwo = $this->prophesize(DifficultyDisabilityRecord::class);
        $recordTwo->getCode()->willReturn('DDCODE2');
        $recordTwo = $recordTwo->reveal();

        $records = [
            $recordOne,
            $recordTwo
        ];

        $repo = $this->prophesize(DifficultyDisabilityRecordRepository::class);
        $repo->findByStudentAndAcademicYear(Argument::exact($student), Argument::exact($this->academicYear))->willReturn($records);
        $this->orm->getRepository(DifficultyDisabilityRecord::class)->willReturn($repo->reveal());

        $this->generalCodeUtil
            ->getDescription(
                Argument::exact('DDCODE1'),
                Argument::exact(GeneralCode::CATEGORY_STUDENT_DIFFICULTY_DISABILITY)
            )
            ->shouldBeCalled()->willReturn('DD Code 1');

        $this->generalCodeUtil
            ->getDescription(
                Argument::exact('DDCODE2'),
                Argument::exact(GeneralCode::CATEGORY_STUDENT_DIFFICULTY_DISABILITY)
            )
            ->shouldBeCalled()->willReturn('DD Code 2');

        $extension = $this->createExtension();
        $descriptions = $extension->participantDifficultiesAndDisabilities($studentParticipant);

        $this->assertCount(2, $descriptions);
        $this->assertContains('DD Code 1', $descriptions);
        $this->assertContains('DD Code 2', $descriptions);
    }

    public function testParticipantDietaryRequirements()
    {
        $studentParticipant = $this->studentParticipant;
        $student = $this->student;
        $osaConsent = $this->prophesize(Note::class)->reveal();
        $value = 'Dietary Requirements';

        $repo = $this->prophesize(NoteRepository::class);
        $repo->findOneOsaConsentByStudent(Argument::exact($student))->willReturn($osaConsent);
        $this->orm->getRepository(Note::class)->willReturn($repo->reveal());

        $this->personalRecordUtil
            ->getOptionDataValue(
                Argument::exact($osaConsent),
                Argument::exact(Option::OSA_CONSENT_DIETARY_REQUIREMENTS_ID)
            )
            ->shouldBeCalled()->willReturn($value);

        $extension = $this->createExtension();

        $this->assertSame($value, $extension->participantDietaryRequirements($studentParticipant));
        // Test cache
        $this->assertSame($value, $extension->participantDietaryRequirements($studentParticipant));
    }

    public function testParticipantMedication()
    {
        $studentParticipant = $this->studentParticipant;
        $student = $this->student;
        $osaConsent = $this->prophesize(Note::class)->reveal();
        $value = 'Medication';

        $repo = $this->prophesize(NoteRepository::class);
        $repo->findOneOsaConsentByStudent(Argument::exact($student))->willReturn($osaConsent);
        $this->orm->getRepository(Note::class)->willReturn($repo->reveal());

        $this->personalRecordUtil
            ->getOptionDataValue(
                Argument::exact($osaConsent),
                Argument::exact(Option::OSA_CONSENT_MEDICATION_ID)
            )
            ->shouldBeCalled()->willReturn($value);

        $extension = $this->createExtension();

        $this->assertSame($value, $extension->participantMedication($studentParticipant));
        // Test cache
        $this->assertSame($value, $extension->participantMedication($studentParticipant));
    }

    public function testParticipantAlcoholConsent()
    {
        $studentParticipant = $this->studentParticipant;
        $student = $this->student;
        $osaConsent = $this->prophesize(Note::class)->reveal();
        $value = true;

        $repo = $this->prophesize(NoteRepository::class);
        $repo->findOneOsaConsentByStudent(Argument::exact($student))->willReturn($osaConsent);
        $this->orm->getRepository(Note::class)->willReturn($repo->reveal());

        $this->personalRecordUtil
            ->getOptionDataValue(
                Argument::exact($osaConsent),
                Argument::exact(Option::OSA_CONSENT_ALCOHOL_ID)
            )
            ->shouldBeCalled()->willReturn($value);

        $extension = $this->createExtension();

        $this->assertSame($value, $extension->participantAlcoholConsent($studentParticipant));
        // Test cache
        $this->assertSame($value, $extension->participantAlcoholConsent($studentParticipant));
    }

    public function testGetName()
    {
        $extension = $this->createExtension();
        $name = $extension->getName();
        $this->assertSame('cis_educational_visit_extension', $name);
    }
}