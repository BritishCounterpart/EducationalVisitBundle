<?php


namespace Cis\EducationalVisitBundle\Messenger;

use App\Entity\User;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Component\Messenger\MessengerInterface;

class EducationalVisitMessenger
{
    const FINANCE_EMAIL_ADDRESS = 'financeoffice@petroc.ac.uk';

    private $messenger;

    public function __construct(MessengerInterface $messenger)
    {
        $this->messenger = $messenger;
    }

    public function sendFinanceCodesEmail(Visit $visit)
    {
        $this->messenger->createAndSend('email', [
            'template' => '@CisEducationalVisit/Messenger/financeCodesEmail.html.twig',
            'template_data' => [
                'visit' => $visit
            ],
            'recipients' => self::FINANCE_EMAIL_ADDRESS
        ]);
    }

    public function sendFinancePaymentAmountChangeEmail(Visit $visit, Visit $original)
    {
        $this->messenger->createAndSend('email', [
            'template' => '@CisEducationalVisit/Messenger/financePaymentAmountChangeEmail.html.twig',
            'template_data' => [
                'visit' => $visit,
                'original' => $original
            ],
            'recipients' => self::FINANCE_EMAIL_ADDRESS
        ]);
    }

    public function sendPaymentCompleteEmail(StudentParticipant $participant)
    {
        $visit = $participant->getVisit();
        $student = $participant->getStudent();
        $this->messenger->createAndSend('email', [
            'template' => '@CisEducationalVisit/Messenger/paymentCompleteEmail.html.twig',
            'template_data' => [
                'visit' => $visit,
                'student' => $student
            ],
            'recipients' => $student->getEmail(),
            'reply_to' => $visit->getPaymentCompleteEmailReplyTo()
        ]);
    }

    public function sendVisitApprovedEmail(User $user, Visit $visit)
    {
        $this->messenger->createAndSend('email', [
            'template' => '@CisEducationalVisit/Messenger/visitApprovedEmail.html.twig',
            'template_data' => [
                'visit' => $visit,
                'user' => $user
            ],
            'recipients' => $visit->getOrganiser()->getEmail(),
            'reply_to' => $user->getEmail()
        ]);
    }

    public function sendVisitRejectedEmail(User $user, Visit $visit, string $reason)
    {
        $this->messenger->createAndSend('email', [
            'template' => '@CisEducationalVisit/Messenger/visitRejectedEmail.html.twig',
            'template_data' => [
                'visit' => $visit,
                'user' => $user,
                'reason' => $reason
            ],
            'recipients' => $visit->getOrganiser()->getEmail(),
            'reply_to' => $user->getEmail()
        ]);
    }

    public function sendExpensesChangedOnApprovedVisit($originalExpenses, Visit $visit)
    {
        $this->messenger->createAndSend('email', [
            'template' => '@CisEducationalVisit/Messenger/expensesChangedOnApprovedVisit.html.twig',
            'template_data' => [
                'originalExpenses' => $originalExpenses,
                'visit' => $visit
            ],
            'recipients' => $this->getApproverUserEmailAddresses($visit->getArea()->getApprovalUsers()),
            'reply_to' => $visit->getOrganiser()->getEmail()
        ]);
    }

    public function sendDetailsChangedOnApprovedVisit(Visit $originalVisit, Visit $visit)
    {
        $this->messenger->createAndSend('email', [
            'template' => '@CisEducationalVisit/Messenger/detailsChangedOnApprovedVisit.html.twig',
            'template_data' => [
                'originalVisit' => $originalVisit,
                'visit' => $visit
            ],
            'recipients' => $this->getApproverUserEmailAddresses($visit->getArea()->getApprovalUsers()),
            'reply_to' => $visit->getOrganiser()->getEmail()
        ]);
    }

    private function getApproverUserEmailAddresses($approverUsers)
    {
        $emailAddresses = [];

        foreach($approverUsers as $approverUser)
        {
            $emailAddresses[] = $approverUser->getEmail();
        }

        return $emailAddresses;
    }
}