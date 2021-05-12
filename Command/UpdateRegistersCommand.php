<?php

namespace Cis\EducationalVisitBundle\Command;

use App\Entity\Register\AttendanceMark;
use App\Entity\Register\Session;
use App\Entity\Register\SessionMember;
use App\Entity\Student\Student;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Petroc\Component\Helper\Orm;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateRegistersCommand extends Command
{
    private $orm;
    private $util;
    private $academicYear;
    private $output;
    private $isAbleToGo = [];

    public function __construct(Orm $orm, ParticipantUtil $util, int $academicYear)
    {
        $this->orm = $orm;
        $this->util = $util;
        $this->academicYear = $academicYear;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('cis_educational_visit:update_registers')
            ->setDescription('Update Update Registers From Educational Visits')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $academicYear = $this->academicYear;
        $status = Visit::STATUS_APPROVED;

        $attendanceMark = $this->getAcademicAuthorisedAbsenceMark();

        $registers = $this->getNonRecurrenceRegisters($academicYear, $status);
        $count = count($registers);

        $io->comment(sprintf('Non-recurring visits %s unmarked register sessions found.', $count));
        $io->comment('Attempting to mark non-recurring visits where students are going.');

        $marked = $this->attemptToMark($attendanceMark, $registers);

        $io->comment(sprintf(
            '%s marked and %s not marked that can not go.',
            $marked,
            $count - $marked
        ));

        $registers = $this->getRecurrenceRegisters($academicYear, $status);
        $count = count($registers);

        $io->comment(sprintf('Recurring visits %s unmarked register sessions found.', $count));
        $io->comment('Attempting to mark recurring visits where students are going.');

        $marked = $this->attemptToMark($attendanceMark, $registers);

        $io->comment(sprintf(
            '%s marked and %s not marked that can not go.',
            $marked,
            $count - $marked
        ));

        $registers = $this->getNonRecurrenceMarkedRegisters($academicYear);
        $count = count($registers);

        $io->comment(sprintf('Non-recurring visits %s marked register sessions found.', $count));
        $io->comment('Attempting to unmark non-recurring visits where students are no longer going.');

        $unmarked = $this->attemptToUnmark($registers);

        $io->comment(sprintf(
            '%s register marks found and %s unmarked that can not go anymore.',
            $count,
            $count - $unmarked
        ));

        $registers = $this->getRecurrenceMarkedRegisters($academicYear);
        $count = count($registers);

        $io->comment(sprintf('Recurring visits %s marked register sessions found.', $count));
        $io->comment('Attempting to unmark recurring visits where students are no longer going.');

        $unmarked = $this->attemptToUnmark($registers);

        $io->comment(sprintf(
            '%s register marks found and %s unmarked that can not go anymore.',
            $count,
            $count - $unmarked
        ));

        //$this->orm->flush();
        $io->success('Finished');
    }

    private function attemptToMark(AttendanceMark $attendanceMark, array $registers)
    {
        $count = count($registers);
        $progressBar = new ProgressBar($this->output, $count);
        $progressBar->start();

        $marked = 0;

        if ($count > 0) {
            foreach ($registers as $register) {
                $isABleToGo = $this->isAbleToGo($register['PARTICIPANT']);
                if ($isABleToGo) {
                    $session = $this->getSession($register['SESSION']);
                    $student = $this->getStudent($register['STUDENT']);
                    $sessionMember = $this->orm->getRepository(SessionMember::class)->findOneBySessionAndStudent(
                        $session,
                        $student
                    );
                    if ($sessionMember !== null) {
                        //$sessionMember->setMark($attendanceMark);
                        $marked++;
                    }
                }
                $progressBar->advance();
            }
        }

        return $marked;
    }

    private function attemptToUnmark(array $registers)
    {
        $count = count($registers);
        $progressBar = new ProgressBar($this->output, $count);
        $progressBar->start();

        $unmarked = 0;

        if ($count > 0) {
            foreach ($registers as $register) {
                if($register['DELETED'] == 0) {
                    $isABleToGo = $this->isAbleToGo($register['PARTICIPANT']);
                } else {
                    $isABleToGo = false;
                }
                if ($isABleToGo == false) {
                    $session = $this->getSession($register['SESSION']);
                    $student = $this->getStudent($register['STUDENT']);
                    $sessionMember = $this->orm->getRepository(SessionMember::class)->findOneBySessionAndStudent(
                        $session,
                        $student
                    );
                    if ($sessionMember !== null) {
                        //$sessionMember->setMark(null);
                        $unmarked++;
                    }
                }
                $progressBar->advance();
            }
        }

        return $unmarked;
    }

    private function getAcademicAuthorisedAbsenceMark()
    {
        return $this->orm->findOrError(
            AttendanceMark::class,
            AttendanceMark::ACADEMIC_AUTHORISED_ABSENCE
        );
    }

    private function isAbleToGo(int $participantId)
    {
        // Cached to speed up, since participant is called for every register mark.
        $isAbleToGo = $this->isAbleToGo;
        if (array_key_exists($participantId, $isAbleToGo)) {
            return $isAbleToGo[$participantId];
        }

        $studentParticipant = $this->orm->findOrError(StudentParticipant::class, $participantId);
        $util = $this->util;
        $missingOsaConsent = $util->getMissingOSAConsent($studentParticipant);
        $payment = $util->getStudentPayment($studentParticipant);

        return $this->isAbleToGo[$participantId] = $util->isAbleToGo($studentParticipant, $missingOsaConsent, $payment);
    }

    private function getStudent(int $id)
    {
        return $this->orm->findOrError(Student::class, $id);
    }

    private function getSession(int $id)
    {
        return $this->orm->findOrError(Session::class, $id);
    }

    private function getNonRecurrenceRegisters(int $academicYear, string $status)
    {
        $conn = $this->orm->getConnection();

        $sql = "SELECT
                    ev.OBJECT_ID VISIT,
                    evsp.OBJECT_ID PARTICIPANT,
                    evsp.STUDENT,
                    CONVERT(DATETIME,ev.START_DATE) + CONVERT(DATETIME, ISNULL(ev.START_TIME,CONVERT(TIME, '00:00:00'))) VISIT_START,
                    CONVERT(DATETIME,ISNULL(ev.END_DATE, ev.START_DATE)) + CONVERT(DATETIME, ISNULL(ev.END_TIME,CONVERT(TIME, '11:59:59'))) VISIT_END,
                    rs.object_id SESSION,
                    sm.mark,
                    rs.date + CONVERT(DATETIME,CONVERT(TIME, rs.start_time)) SESSION_START,
                    rs.date + CONVERT(DATETIME,CONVERT(TIME, DATEADD(mi, rs.duration,rs.start_time))) SESSION_END
                FROM
                     PETROC_EDUCATIONAL_VISIT ev
                JOIN PETROC_EDUCATIONAL_VISIT_STUDENT_PARTICIPANT evsp ON evsp.PETROC_EDUCATIONAL_VISIT = ev.OBJECT_ID
                JOIN petroc_v_register_header_member hm ON hm.student = evsp.STUDENT
                JOIN petroc_v_register_session_member sm ON sm.register_header_member = hm.object_id
                JOIN petroc_v_register_session rs ON rs.object_id = sm.register_session
                WHERE
                    ev.ACADEMIC_YEAR >= $academicYear
                AND ev.START_DATE >= GETDATE()
                AND ev.RECURRENCE_PATTERN IS NULL
                AND ev.STATUS = '$status'
                AND ev.DELETED_ON IS NULL 
                AND evsp.DELETED_ON IS NULL 
                AND evsp.NO_LONGER_GOING = 0
                -- Session start between visit start and end
                AND (rs.date + CONVERT(DATETIME,CONVERT(TIME, rs.start_time)))
                    BETWEEN (CONVERT(DATETIME,ev.START_DATE) + CONVERT(DATETIME, ISNULL(ev.START_TIME,CONVERT(TIME, '00:00:00'))))
                    AND (CONVERT(DATETIME,ISNULL(ev.END_DATE, ev.START_DATE)) + CONVERT(DATETIME, ISNULL(ev.END_TIME,CONVERT(TIME, '11:59:59'))))
                -- Session end between visit start and end
                AND rs.date + CONVERT(DATETIME,CONVERT(TIME, DATEADD(mi, rs.duration,rs.start_time)))
                    BETWEEN (CONVERT(DATETIME,ev.START_DATE) + CONVERT(DATETIME, ISNULL(ev.START_TIME,CONVERT(TIME, '00:00:00'))))
                    AND (CONVERT(DATETIME,ISNULL(ev.END_DATE, ev.START_DATE)) + CONVERT(DATETIME, ISNULL(ev.END_TIME,CONVERT(TIME, '11:59:59'))))
                AND sm.mark IS NULL
                ORDER BY
                    evsp.OBJECT_ID,
                    ev.OBJECT_ID";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getNonRecurrenceMarkedRegisters(int $academicYear)
    {
        $conn = $this->orm->getConnection();

        $sql = "SELECT
                    ev.OBJECT_ID VISIT,
                    evsp.OBJECT_ID PARTICIPANT,
                    CASE WHEN ev.DELETED_ON IS NOT NULL OR evsp.DELETED_ON IS NOT NULL OR ev.status = 'Cancelled' THEN 1 ELSE 0 END DELETED,
                    evsp.STUDENT,
                    CONVERT(DATETIME,ev.START_DATE) + CONVERT(DATETIME, ISNULL(ev.START_TIME,CONVERT(TIME, '00:00:00'))) VISIT_START,
                    CONVERT(DATETIME,ISNULL(ev.END_DATE, ev.START_DATE)) + CONVERT(DATETIME, ISNULL(ev.END_TIME,CONVERT(TIME, '11:59:59'))) VISIT_END,
                    rs.object_id SESSION,
                    sm.mark,
                    rs.date + CONVERT(DATETIME,CONVERT(TIME, rs.start_time)) SESSION_START,
                    rs.date + CONVERT(DATETIME,CONVERT(TIME, DATEADD(mi, rs.duration,rs.start_time))) SESSION_END
                FROM
                     PETROC_EDUCATIONAL_VISIT ev
                JOIN PETROC_EDUCATIONAL_VISIT_STUDENT_PARTICIPANT evsp ON evsp.PETROC_EDUCATIONAL_VISIT = ev.OBJECT_ID
                JOIN petroc_v_register_header_member hm ON hm.student = evsp.STUDENT
                JOIN petroc_v_register_session_member sm ON sm.register_header_member = hm.object_id
                JOIN petroc_v_register_session rs ON rs.object_id = sm.register_session
                WHERE
                    ev.ACADEMIC_YEAR >= $academicYear
                AND ev.START_DATE >= GETDATE()
                AND sm.mark = 'A'
                AND ev.RECURRENCE_PATTERN IS NULL
                AND evsp.OBJECT_ID = (
                    SELECT
                        MAX(evsp2.OBJECT_ID)
                    FROM
                        PETROC_EDUCATIONAL_VISIT_STUDENT_PARTICIPANT evsp2
                    WHERE
                        evsp2.PETROC_EDUCATIONAL_VISIT = evsp.PETROC_EDUCATIONAL_VISIT
                    AND evsp2.STUDENT = evsp.STUDENT
                )";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getRecurrenceRegisters(int $academicYear, string $status)
    {
        $conn = $this->orm->getConnection();

        $sql = "SELECT
                    ev.OBJECT_ID VISIT,
                    evsp.OBJECT_ID PARTICIPANT,
                    evsp.STUDENT,
                    ev.RECURRENCE_PATTERN,
                    ev.VISIT_FROM,
                    ev.VISIT_UNTIL,
                    ev.VISIT_WEEKLY_DAY,
                    ev.VISIT_WEEKLY_START,
                    ev.VISIT_WEEKLY_END,
                    rs.object_id SESSION,
                    sm.mark,
                    rs.date + CONVERT(DATETIME,CONVERT(TIME, rs.start_time)) SESSION_START,
                    rs.date + CONVERT(DATETIME,CONVERT(TIME, DATEADD(mi, rs.duration,rs.start_time))) SESSION_END
                FROM
                    (SELECT
                        ev.OBJECT_ID,
                        ev.ACADEMIC_YEAR,
                        CONVERT(DATETIME,ev.START_DATE) + CONVERT(DATETIME, ISNULL(ev.START_TIME,CONVERT(TIME, '00:00:00'))) VISIT_FROM,
                        CONVERT(DATETIME,ISNULL(ev.END_DATE, ev.START_DATE)) + CONVERT(DATETIME, ISNULL(ev.END_TIME,CONVERT(TIME, '11:59:59'))) VISIT_UNTIL,
                        ev.RECURRENCE_PATTERN,
                        d.day VISIT_WEEKLY_DAY,
                        d.date + CONVERT(DATETIME, ISNULL(ev.START_TIME,CONVERT(TIME, '00:00:00'))) VISIT_WEEKLY_START,
                        d.date + CONVERT(DATETIME, ISNULL(ev.END_TIME,CONVERT(TIME, '11:59:59'))) VISIT_WEEKLY_END
                    FROM
                        petroc_v_day d
                    JOIN PETROC_EDUCATIONAL_VISIT ev
                        ON CONVERT(DATE, d.date) BETWEEN ev.START_DATE
                        AND ev.END_DATE
                        AND d.academic_year = ev.ACADEMIC_YEAR
                        AND LOWER(d.day) = LOWER(ev.RECURRENCE_PATTERN)
                        AND d.closed_day = 0
                    WHERE
                        ev.ACADEMIC_YEAR >= $academicYear
                    AND ev.START_DATE >= GETDATE()
                    AND ev.DELETED_ON IS NULL
                    AND ev.RECURRENCE_PATTERN IS NOT NULL
                    AND ev.STATUS = '$status'
                    AND ev.END_DATE IS NOT NULL) ev
                JOIN PETROC_EDUCATIONAL_VISIT_STUDENT_PARTICIPANT evsp ON evsp.PETROC_EDUCATIONAL_VISIT = ev.OBJECT_ID
                JOIN petroc_v_register_header_member hm ON hm.student = evsp.STUDENT
                JOIN petroc_v_register_session_member sm ON sm.register_header_member = hm.object_id
                JOIN petroc_v_register_session rs ON rs.object_id = sm.register_session
                WHERE
                    evsp.DELETED_ON IS NULL
                AND evsp.NO_LONGER_GOING = 0
                -- Session start between visit weekly start and weekly end
                AND (rs.date + CONVERT(DATETIME,CONVERT(TIME, rs.start_time)))
                    BETWEEN VISIT_WEEKLY_START
                    AND VISIT_WEEKLY_END
                -- Session end between visit weekly start and weekly end
                AND rs.date + CONVERT(DATETIME,CONVERT(TIME, DATEADD(mi, rs.duration,rs.start_time)))
                    BETWEEN VISIT_WEEKLY_START
                    AND VISIT_WEEKLY_END
                AND sm.mark IS NULL
                ORDER BY
                    evsp.OBJECT_ID,
                    ev.OBJECT_ID,
                    ev.VISIT_WEEKLY_START";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getRecurrenceMarkedRegisters(int $academicYear)
    {
        $conn = $this->orm->getConnection();

        $sql = "SELECT
                    ev.OBJECT_ID VISIT,
                    evsp.OBJECT_ID PARTICIPANT,
                    CASE WHEN ev.DELETED_ON IS NOT NULL OR evsp.DELETED_ON IS NOT NULL OR ev.status = 'Cancelled' THEN 1 ELSE 0 END DELETED,
                    evsp.STUDENT,
                    ev.RECURRENCE_PATTERN,
                    ev.VISIT_FROM,
                    ev.VISIT_UNTIL,
                    ev.VISIT_WEEKLY_DAY,
                    ev.VISIT_WEEKLY_START,
                    ev.VISIT_WEEKLY_END,
                    rs.object_id SESSION,
                    sm.mark,
                    rs.date + CONVERT(DATETIME,CONVERT(TIME, rs.start_time)) SESSION_START,
                    rs.date + CONVERT(DATETIME,CONVERT(TIME, DATEADD(mi, rs.duration,rs.start_time))) SESSION_END
                FROM
                    (SELECT
                        ev.OBJECT_ID,
                        ev.ACADEMIC_YEAR,
                        ev.DELETED_ON,
                        ev.STATUS,
                        CONVERT(DATETIME,ev.START_DATE) + CONVERT(DATETIME, ISNULL(ev.START_TIME,CONVERT(TIME, '00:00:00'))) VISIT_FROM,
                        CONVERT(DATETIME,ISNULL(ev.END_DATE, ev.START_DATE)) + CONVERT(DATETIME, ISNULL(ev.END_TIME,CONVERT(TIME, '11:59:59'))) VISIT_UNTIL,
                        ev.RECURRENCE_PATTERN,
                        d.day VISIT_WEEKLY_DAY,
                        d.date + CONVERT(DATETIME, ISNULL(ev.START_TIME,CONVERT(TIME, '00:00:00'))) VISIT_WEEKLY_START,
                        d.date + CONVERT(DATETIME, ISNULL(ev.END_TIME,CONVERT(TIME, '11:59:59'))) VISIT_WEEKLY_END
                    FROM
                        petroc_v_day d
                    JOIN PETROC_EDUCATIONAL_VISIT ev
                        ON CONVERT(DATE, d.date) BETWEEN ev.START_DATE
                        AND ev.END_DATE
                        AND d.academic_year = ev.ACADEMIC_YEAR
                        AND LOWER(d.day) = LOWER(ev.RECURRENCE_PATTERN)
                        AND d.closed_day = 0
                    WHERE
                        ev.ACADEMIC_YEAR >= $academicYear
                    AND ev.START_DATE >= GETDATE()
                    AND ev.RECURRENCE_PATTERN IS NOT NULL
                    AND ev.END_DATE IS NOT NULL) ev
                JOIN PETROC_EDUCATIONAL_VISIT_STUDENT_PARTICIPANT evsp ON evsp.PETROC_EDUCATIONAL_VISIT = ev.OBJECT_ID
                JOIN petroc_v_register_header_member hm ON hm.student = evsp.STUDENT
                JOIN petroc_v_register_session_member sm ON sm.register_header_member = hm.object_id
                JOIN petroc_v_register_session rs ON rs.object_id = sm.register_session
                WHERE
                -- Session start between visit weekly start and weekly end
                    (rs.date + CONVERT(DATETIME,CONVERT(TIME, rs.start_time)))
                    BETWEEN VISIT_WEEKLY_START
                    AND VISIT_WEEKLY_END
                -- Session end between visit weekly start and weekly end
                AND rs.date + CONVERT(DATETIME,CONVERT(TIME, DATEADD(mi, rs.duration,rs.start_time)))
                    BETWEEN VISIT_WEEKLY_START
                    AND VISIT_WEEKLY_END
                AND sm.mark = 'A'
                AND evsp.OBJECT_ID = (
                    SELECT
                        MAX(evsp2.OBJECT_ID)
                    FROM
                        PETROC_EDUCATIONAL_VISIT_STUDENT_PARTICIPANT evsp2
                    WHERE
                        evsp2.PETROC_EDUCATIONAL_VISIT = evsp.PETROC_EDUCATIONAL_VISIT
                    AND evsp2.STUDENT = evsp.STUDENT
                )
                ORDER BY
                    evsp.OBJECT_ID,
                    ev.OBJECT_ID,
                    ev.VISIT_WEEKLY_START";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}