<?php

namespace Cis\EducationalVisitBundle\Tests\Form\Payment;

use App\Tests\FormTypeTestCase;
use Cis\EducationalVisitBundle\Form\Payment\ChangeStudentParticipantPaymentAmountFormType;

class ChangeStudentParticipantPaymentAmountFormTypeTest extends FormTypeTestCase
{
    public function testCompile()
    {
        $form = $this->factory->create(ChangeStudentParticipantPaymentAmountFormType::class);
        $view = $form->createView();
        $this->assertArrayHasKey('fullPaymentAmount', $view);
        $this->assertArrayHasKey('firstPaymentAmount', $view);
        $this->assertCount(2, $view);
    }
}