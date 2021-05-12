<?php

namespace Cis\EducationalVisitBundle\Tests\Form\Type;

use App\Tests\FormTypeTestCase;
use Cis\EducationalVisitBundle\Form\Type\AreaType;
use Cis\EducationalVisitBundle\Tests\Fixture\AreaTableFixture;

class AreaTypeTest extends FormTypeTestCase
{
    protected function getTableFixtures()
    {
        return [
            new AreaTableFixture()
        ];
    }

    public function testCompile()
    {
        $form = $this->factory->create(AreaType::class, null);

        $view = $form->createView();

        $expected = [
            'Health Care' => (string) AreaTableFixture::HEALTH_CARE_ID,
            'Student Support' => (string) AreaTableFixture::STUDENT_SUPPORT_ID
        ];

        $this->assertViewChoices($expected, $view->vars['choices']);
    }
}