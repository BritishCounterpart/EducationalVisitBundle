<?php

namespace Cis\EducationalVisitBundle\Tests\Form\Type;

use App\Entity\Misc\GeneralCode;
use App\Tests\EmptyEntityTableFixture;
use App\Tests\FormTypeTestCase;
use Cis\EducationalVisitBundle\Form\Type\ExpenseType;

class ExpenseTypeTest extends FormTypeTestCase
{
    public function testCompile()
    {
        $form = $this->factory->create(ExpenseType::class);
        $view = $form->createView();
        $this->assertArrayHasKey('type', $view);
        $this->assertArrayHasKey('description', $view);
        $this->assertArrayHasKey('amount', $view);
        $this->assertCount(3, $view);
    }

    protected function getTableFixtures()
    {
        return [
            new EmptyEntityTableFixture(GeneralCode::class),
        ];
    }
}