<?php

namespace Cis\EducationalVisitBundle\Tests\Form\Itinerary;

use App\Tests\FormTypeTestCase;
use Cis\EducationalVisitBundle\Form\Itinerary\UploadFormType;

class UploadFormTypeTest extends FormTypeTestCase
{
    public function testCompile()
    {
        $form = $this->factory->create(UploadFormType::class);
        $view = $form->createView();
        $this->assertArrayHasKey('file', $view);
        $this->assertCount(1, $view);
    }
}