<?php

namespace Cis\EducationalVisitBundle\Tests\Form\Detail;

use App\Tests\FormTypeTestCase;
use Cis\EducationalVisitBundle\Form\Detail\EditFinanceCodesFormType;

class EditFinanceCodesFormTypeTest extends FormTypeTestCase
{
    public function testCompile()
    {
        $form = $this->factory->create(EditFinanceCodesFormType::class);
        $view = $form->createView();

        $this->assertArrayHasKey('evNumber', $view);
        $this->assertArrayHasKey('costCode', $view);
        $this->assertCount(2, $view);
    }
}