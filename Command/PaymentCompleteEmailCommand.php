<?php

namespace Cis\EducationalVisitBundle\Command;

use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Messenger\EducationalVisitMessenger;
use Cis\EducationalVisitBundle\Util\ParticipantUtil;
use Petroc\Component\Helper\Orm;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PaymentCompleteEmailCommand extends Command
{
    private $orm;
    private $util;
    private $messenger;
    private $academicYear;

    public function __construct(Orm $orm, ParticipantUtil $util, EducationalVisitMessenger $messenger, int $academicYear)
    {
        $this->orm = $orm;
        $this->util = $util;
        $this->messenger = $messenger;
        $this->academicYear = $academicYear;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('cis_educational_visit:payment_complete_email')
            ->setDescription('Educational Visits Send Payment Complete Email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $orm = $this->orm;

        $participants = $orm
            ->createQueryBuilder()
            ->select('sp','v','s')
            ->from(StudentParticipant::class, 'sp')
            ->join('sp.visit', 'v')
            ->join('sp.student', 's')
            ->where('sp.paymentCompleteEmailSent = 0')
            ->andWhere('v.paymentCompleteEmailSubject IS NOT NULL')
            ->andWhere('v.paymentCompleteEmailContent IS NOT NULL')
            ->andWhere('v.paymentCompleteEmailReplyTo IS NOT NULL')
            ->andWhere('v.academicYear >= :academic_year')
            ->setParameter('academic_year', $this->academicYear)
            ->andWhere('v.fullPaymentAmount > 0')
            ->andWhere('v.status = :status')
            ->setParameter('status', Visit::STATUS_APPROVED)
            ->getQuery()
            ->getResult()
        ;

        $emailsSent = 0;

        foreach($participants as $participant)
        {
            $hasPaid = $this->hasPaid($participant);
            if($hasPaid === true) {
                $this->messenger->sendPaymentCompleteEmail($participant);
                $participant->setPaymentCompleteEmailSent(true);
                $emailsSent++;
            }
        }

        $io->comment(sprintf(
            '%s emails sent.',
            $emailsSent
        ));

        $this->orm->flush();
        $io->success('Finished');
    }

    public function hasPaid(StudentParticipant $participant)
    {
        return $this->util->getStudentPayment($participant)['paid'];
    }
}