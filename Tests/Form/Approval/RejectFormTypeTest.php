<?php

namespace Cis\EducationalVisitBundle\Tests\Form\Approval;

use App\Tests\FormTypeTestCase;
use Cis\EducationalVisitBundle\Form\Approval\RejectFormType;

class RejectFormTypeTest extends FormTypeTestCase
{
    public function testCompile()
    {
        $form = $this->factory->create(RejectFormType::class);
        $view = $form->createView();
        $this->assertArrayHasKey('reason', $view);
        $this->assertCount(1, $view);
    }
}