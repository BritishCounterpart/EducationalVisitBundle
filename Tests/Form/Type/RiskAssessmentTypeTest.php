<?php

namespace Cis\EducationalVisitBundle\Tests\Form\Type;

use App\Tests\Fixture\Misc\RiskAssessmentTableFixture;
use App\Tests\FormTypeTestCase;
use Cis\EducationalVisitBundle\Form\Type\RiskAssessmentType;

class RiskAssessmentTypeTest extends FormTypeTestCase
{
    protected function getTableFixtures()
    {
        return [
            new RiskAssessmentTableFixture()
        ];
    }

    public function testCompile()
    {
        $form = $this->factory->create(RiskAssessmentType::class, null);

        $view = $form->createView();

        foreach($view->vars['choices'] as $choice) {
            $this->assertContains($choice->label, ['Generic','Other']);
        }

        $genericRiskAssessment = [
            RiskAssessmentTableFixture::TRIP_A_ID,
            RiskAssessmentTableFixture::TRIP_B_ID,
            RiskAssessmentTableFixture::TRIP_C_ID
        ];

        $genericChoices = $view->vars['choices']['Generic'];
        $this->assertCount(3, $genericChoices);
        foreach($genericChoices as $genericChoice) {
            $this->assertContains($genericChoice->value, $genericRiskAssessment);
        }

        $otherChoices = $view->vars['choices']['Other'];
        $this->assertCount(1, $otherChoices);
        foreach($otherChoices as $otherChoices) {
            $this->assertNotContains($otherChoices->value, $genericRiskAssessment);
        }
    }
}