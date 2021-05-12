<?php

namespace Cis\EducationalVisitBundle\Tests\Form\Payment;

use App\Tests\FormTypeTestCase;
use Cis\EducationalVisitBundle\Form\Payment\FilterFormType;

class FilterFormTypeTest extends FormTypeTestCase
{
    public function testCompile()
    {
        $form = $this->factory->create(FilterFormType::class);
        $view = $form->createView();
        $this->assertArrayHasKey('paymentStatus', $view);
        $this->assertArrayHasKey('showNoLongerGoing', $view);
        $this->assertCount(2, $view);
    }
}