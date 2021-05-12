<?php

namespace Cis\EducationalVisitBundle\Command;

use Cis\EducationalVisitBundle\CommandBus\Participant\RefreshStudentsCommand;
use Cis\EducationalVisitBundle\CommandBus\Participant\RefreshStudentsHandler;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Petroc\Component\Helper\Orm;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RefreshStudentParticipantsCommand extends Command
{
    private $orm;
    private $util;
    private $academicYear;

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
            ->setName('cis_educational_visit:refresh_student_participants')
            ->setDescription('Refresh Student Participants');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $orm = $this->orm;

        $visits = $orm
            ->createQueryBuilder()
            ->select('v')
            ->from(Visit::class, 'v')
            ->where('v.academicYear >= :academic_year')
            ->setParameter('academic_year', $this->academicYear)
            ->andWhere('v.status NOT IN (:statuses)')
            ->setParameter('statuses', [Visit::STATUS_CANCELLED, Visit::STATUS_COMPLETED])
            ->andWhere('v.startDate >= CURRENT_DATE()')
            ->getQuery()
            ->getResult();

        $count = count($visits);

        $io->comment(sprintf(
            '%s visits to refresh.',
            count($visits)
        ));

        $progressBar = new ProgressBar($output, $count);
        $progressBar->start();

        foreach ($visits as $visit) {
            $handler = new RefreshStudentsHandler($orm, $this->util);
            $handler->handle(new RefreshStudentsCommand($visit));
            $progressBar->advance();
        }

        $progressBar->finish();

        $orm->flush();

        $io->success('Finished');
    }
}