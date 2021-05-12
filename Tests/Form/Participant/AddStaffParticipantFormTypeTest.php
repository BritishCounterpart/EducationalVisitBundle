<?php

namespace Cis\EducationalVisitBundle\Tests\Form\Participant;

use App\Entity\Employee\Employee;
use App\Entity\User;
use App\Tests\EmptyEntityTableFixture;
use App\Tests\FormTypeTestCase;
use Cis\EducationalVisitBundle\Form\Participant\AddStaffParticipantFormType;

class AddStaffParticipantFormTypeTest extends FormTypeTestCase
{
    public function testCompile()
    {
        $form = $this->factory->create(AddStaffParticipantFormType::class);
        $view = $form->createView();
        $this->assertArrayHasKey('employee', $view);
        $this->assertCount(1, $view);
    }

    protected function getTableFixtures()
    {
        return [
            new EmptyEntityTableFixture(Employee::class),
            new EmptyEntityTableFixture(User::class)
        ];
    }
}