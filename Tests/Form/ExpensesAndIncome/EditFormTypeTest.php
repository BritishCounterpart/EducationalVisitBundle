<?php

namespace Cis\EducationalVisitBundle\Tests\Form\ExpensesAndIncome;

use App\Entity\Misc\GeneralCode;
use App\Tests\EmptyEntityTableFixture;
use App\Tests\FormTypeTestCase;
use Cis\EducationalVisitBundle\Form\ExpensesAndIncome\EditFormType;

class EditFormTypeTest extends FormTypeTestCase
{
    public function testCompile()
    {
        $form = $this->factory->create(EditFormType::class);
        $view = $form->createView();
        $this->assertArrayHasKey('anyExpenses', $view);
        $this->assertArrayHasKey('expenses', $view);
        $this->assertArrayHasKey('anyIncome', $view);
        $this->assertArrayHasKey('studentsPays', $view);
        $this->assertArrayHasKey('collegePays', $view);
        $this->assertArrayHasKey('otherPayInfo', $view);
        $this->assertArrayHasKey('otherPay', $view);
        $this->assertCount(7, $view);
    }

    protected function getTableFixtures()
    {
        return [
            new EmptyEntityTableFixture(GeneralCode::class),
        ];
    }
}