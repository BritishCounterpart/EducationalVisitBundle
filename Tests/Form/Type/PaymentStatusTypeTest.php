<?php

namespace Cis\EducationalVisitBundle\Tests\Form\Type;

use App\Tests\FormTypeTestCase;
use Cis\EducationalVisitBundle\Form\Type\PaymentStatusType;
use Cis\EducationalVisitBundle\View\AbstractFilterableStudentParticipantList;

class PaymentStatusTypeTest extends FormTypeTestCase
{
    public function testCompile()
    {
        $form = $this->factory->create(PaymentStatusType::class);
        $view = $form->createView();

        $expected = [
            AbstractFilterableStudentParticipantList::PAYMENT_STATUS_PAID,
            AbstractFilterableStudentParticipantList::PAYMENT_STATUS_PARTIAL_PAYMENT,
            AbstractFilterableStudentParticipantList::PAYMENT_STATUS_REFUNDED,
            AbstractFilterableStudentParticipantList::PAYMENT_STATUS_PARTIAL_REFUND,
            AbstractFilterableStudentParticipantList::PAYMENT_STATUS_NOT_PAID
        ];

        $this->assertViewChoices(array_combine($expected,$expected), $view->vars['choices']);
    }
}