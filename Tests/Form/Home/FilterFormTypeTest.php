<?php

namespace Cis\EducationalVisitBundle\Tests\Form\Home;

use App\Entity\Employee\Employee;
use App\Entity\Misc\GeneralCode;
use App\Entity\User;
use App\Tests\EmptyEntityTableFixture;
use App\Tests\FormTypeTestCase;
use Cis\EducationalVisitBundle\Entity\Area;
use Cis\EducationalVisitBundle\Form\Home\FilterFormType;

class FilterFormTypeTest extends FormTypeTestCase
{
    public function testCompile()
    {
        $form = $this->factory->create(FilterFormType::class);
        $view = $form->createView();
        $this->assertArrayHasKey('academicYear', $view);
        $this->assertArrayHasKey('keyword', $view);
        $this->assertArrayHasKey('category', $view);
        $this->assertArrayHasKey('area', $view);
        $this->assertArrayHasKey('organiser', $view);
        $this->assertArrayHasKey('orderBy', $view);
        $this->assertCount(6, $view);
    }

    protected function getTableFixtures()
    {
        return [
            new EmptyEntityTableFixture(Area::class),
            new EmptyEntityTableFixture(Employee::class),
            new EmptyEntityTableFixture(GeneralCode::class),
            new EmptyEntityTableFixture(User::class)

        ];
    }
}