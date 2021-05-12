<?php

namespace Cis\EducationalVisitBundle\Command;

use Petroc\Component\Helper\Orm;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetVisitFullCommand extends Command
{
    private $orm;
    private $academicYear;

    public function __construct(Orm $orm, int $academicYear)
    {
        $this->orm = $orm;
        $this->academicYear = $academicYear;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('cis_educational_visit:set_visit_full')
            ->setDescription('Set Visit Full Flag')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $academicYear = $this->academicYear;

        $sql = "UPDATE v SET
                    v.VISIT_FULL = p.visit_full
                FROM
                    PETROC_EDUCATIONAL_VISIT v
                JOIN
                    (SELECT
                        p.object_number petroc_educational_visit,
                        p.maximum_students maximum_students,
                        count(p.student) number_of_students,
                        CASE WHEN count(p.student) >= p.maximum_students THEN 1 ELSE 0 END visit_full
                    FROM
                        PETROC_V_PETROC_EDUCATIONAL_VISIT_LEGACY_PAYMENT p
                    WHERE
                        p.amount_paid > 0
                    AND p.maximum_students IS NOT NULL
                    AND p.academic_year = $academicYear
                    GROUP BY
                        p.object_number,
                        p.maximum_students
                        ) p ON p.petroc_educational_visit = v.OBJECT_ID
                WHERE
                    v.academic_year = $academicYear";

        $this->orm->getConnection()->executeUpdate($sql);

        $io->success('Finished');
    }
}