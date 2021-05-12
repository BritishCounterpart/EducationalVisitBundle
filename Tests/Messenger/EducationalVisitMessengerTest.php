<?php

namespace Cis\EducationalVisitBundle\Tests\Messenger;

use App\Entity\Employee\Employee;
use App\Entity\Student\Student;
use App\Entity\User;
use Cis\EducationalVisitBundle\Entity\Area;
use Cis\EducationalVisitBundle\Entity\StudentParticipant;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Messenger\EducationalVisitMessenger;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Messenger\MessengerInterface;

class EducationalVisitMessengerTest extends TestCase
{
    private $messenger;

    protected function setUp()
    {
        $this->messenger = $this->prophesize(MessengerInterface::class);
    }

    private function createMessenger()
    {
        return new EducationalVisitMessenger($this->messenger->reveal());
    }

    private function createVisit()
    {
        return $this->prophesize(Visit::class);
    }

    private function createUser()
    {
        return $this->prophesize(User::class);
    }

    private function createEmployee()
    {
        return $this->prophesize(Employee::class);
    }

    private function createArea()
    {
        return $this->prophesize(Area::class);
    }

    public function testSendFinanceCodesEmail()
    {
        $visit = $this->createVisit()->reveal();

        $options = [
            'template' => '@CisEducationalVisit/Messenger/financeCodesEmail.html.twig',
            'template_data' => [
                'visit' => $visit
            ],
            'recipients' => EducationalVisitMessenger::FINANCE_EMAIL_ADDRESS
        ];

        $this->messenger->createAndSend('email', $options)->shouldBeCalled();

        $messenger = $this->createMessenger();
        $messenger->sendFinanceCodesEmail($visit);
    }

    public function testSendFinancePaymentAmountChangeEmail()
    {
        $visit = $this->createVisit()->reveal();
        $original = $this->createVisit()->reveal();

        $options = [
            'template' => '@CisEducationalVisit/Messenger/financePaymentAmountChangeEmail.html.twig',
            'template_data' => [
                'visit' => $visit,
                'original' => $original
            ],
            'recipients' => EducationalVisitMessenger::FINANCE_EMAIL_ADDRESS
        ];

        $this->messenger->createAndSend('email', $options)->shouldBeCalled();

        $messenger = $this->createMessenger();
        $messenger->sendFinancePaymentAmountChangeEmail($visit, $original);
    }

    public function testsendPaymentCompleteEmail()
    {
        $replyTo = 'replyto@test.co.uk';
        $recipient = 'recipients@test.co.uk';

        $visit = $this->createVisit();
        $visit->getPaymentCompleteEmailReplyTo()->willReturn($replyTo);
        $visit = $visit->reveal();

        $student = $this->prophesize(Student::class);
        $student->getEmail()->willReturn($recipient);
        $student = $student->reveal();

        $studentParticipant = $this->prophesize(StudentParticipant::class);
        $studentParticipant->getVisit()->willReturn($visit);
        $studentParticipant->getStudent()->willReturn($student);
        $studentParticipant = $studentParticipant->reveal();

        $options = [
            'template' => '@CisEducationalVisit/Messenger/paymentCompleteEmail.html.twig',
            'template_data' => [
                'visit' => $visit,
                'student' => $student,
            ],
            'recipients' => $recipient,
            'reply_to' => $replyTo,
        ];

        $this->messenger->createAndSend('email', $options)->shouldBeCalled();

        $messenger = $this->createMessenger();
        $messenger->sendPaymentCompleteEmail($studentParticipant);
    }

    public function testSendVisitApprovedEmail()
    {
        $recipientAddress = 'Test@Test.co.uk';
        $replyToAddress = 'Test2@Test.co.uk';

        $organiser = $this->createEmployee();
        $organiser->getEmail()->willReturn($recipientAddress);
        $organiser = $organiser->reveal();

        $visit = $this->createVisit();
        $visit->getOrganiser()->willReturn($organiser);
        $visit = $visit->reveal();

        $user = $this->createUser();
        $user->getEmail()->willReturn($replyToAddress);
        $user = $user->reveal();

        $options = [
            'template' => '@CisEducationalVisit/Messenger/visitApprovedEmail.html.twig',
            'template_data' => [
                'visit' => $visit,
                'user' => $user
            ],
            'recipients' => $recipientAddress,
            'reply_to' => $replyToAddress
        ];

        $this->messenger->createAndSend('email', $options)->shouldBeCalled();

        $messenger = $this->createMessenger();
        $messenger->sendVisitApprovedEmail($user, $visit);
    }

    public function testSendVisitRejectedEmail()
    {
        $recipientAddress = 'Test@Test.co.uk';
        $replyToAddress = 'Test2@Test.co.uk';
        $reason = 'Needs more detail';

        $organiser = $this->createEmployee();
        $organiser->getEmail()->willReturn($recipientAddress);
        $organiser = $organiser->reveal();

        $visit = $this->createVisit();
        $visit->getOrganiser()->willReturn($organiser);
        $visit = $visit->reveal();

        $user = $this->createUser();
        $user->getEmail()->willReturn($replyToAddress);
        $user = $user->reveal();

        $options = [
            'template' => '@CisEducationalVisit/Messenger/visitRejectedEmail.html.twig',
            'template_data' => [
                'visit' => $visit,
                'user' => $user,
                'reason' => $reason
            ],
            'recipients' => $recipientAddress,
            'reply_to' => $replyToAddress
        ];

        $this->messenger->createAndSend('email', $options)->shouldBeCalled();

        $messenger = $this->createMessenger();
        $messenger->sendVisitRejectedEmail($user, $visit, $reason);
    }

    public function testSendExpensesChangedOnApprovedVisit()
    {
        $recipientAddress = 'Test@Test.co.uk';
        $replyToAddress = 'Test2@Test.co.uk';

        $originalExpenses = [1, 2];

        $user = $this->createUser();
        $user->getEmail()->willReturn($recipientAddress);
        $user = $user->reveal();

        $area = $this->createArea();
        $area->getApprovalUsers()->willReturn([$user]);
        $area = $area->reveal();

        $organiser = $this->createEmployee();
        $organiser->getEmail()->willReturn($replyToAddress);
        $organiser = $organiser->reveal();

        $visit = $this->createVisit();
        $visit->getArea()->willReturn($area);
        $visit->getOrganiser()->willReturn($organiser);
        $visit = $visit->reveal();

        $options = [
            'template' => '@CisEducationalVisit/Messenger/expensesChangedOnApprovedVisit.html.twig',
            'template_data' => [
                'originalExpenses' => $originalExpenses,
                'visit' => $visit
            ],
            'recipients' => [$recipientAddress],
            'reply_to' => $replyToAddress
        ];

        $this->messenger->createAndSend('email', $options)->shouldBeCalled();

        $messenger = $this->createMessenger();
        $messenger->sendExpensesChangedOnApprovedVisit($originalExpenses, $visit);
    }

    public function testSendDetailsChangedOnApprovedVisit()
    {
        $recipientAddress = 'Test@Test.co.uk';
        $replyToAddress = 'Test2@Test.co.uk';

        $user = $this->createUser();
        $user->getEmail()->willReturn($recipientAddress);
        $user = $user->reveal();

        $area = $this->createArea();
        $area->getApprovalUsers()->willReturn([$user]);
        $area = $area->reveal();

        $organiser = $this->createEmployee();
        $organiser->getEmail()->willReturn($replyToAddress);
        $organiser = $organiser->reveal();

        $visit = $this->createVisit();
        $visit->getArea()->willReturn($area);
        $visit->getOrganiser()->willReturn($organiser);
        $visit = $visit->reveal();

        $originalVisit = $this->createVisit()->reveal();

        $options = [
            'template' => '@CisEducationalVisit/Messenger/detailsChangedOnApprovedVisit.html.twig',
            'template_data' => [
                'originalVisit' => $originalVisit,
                'visit' => $visit
            ],
            'recipients' => [$recipientAddress],
            'reply_to' => $replyToAddress
        ];

        $this->messenger->createAndSend('email', $options)->shouldBeCalled();

        $messenger = $this->createMessenger();
        $messenger->sendDetailsChangedOnApprovedVisit($originalVisit, $visit);
    }
}
