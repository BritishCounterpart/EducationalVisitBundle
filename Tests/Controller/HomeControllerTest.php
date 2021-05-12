<?php

namespace Cis\EducationalVisitBundle\Tests\Controller;

use App\Entity\User;
use Cis\EducationalVisitBundle\CommandBus\Home\FilterCommand;
use Cis\EducationalVisitBundle\Controller\Controller;
use Cis\EducationalVisitBundle\Controller\HomeController;
use Cis\EducationalVisitBundle\Form\Home\FilterFormType;
use Cis\EducationalVisitBundle\View\DashboardList;
use Petroc\Bridge\PhpUnit\ControllerTestCase;

class HomeControllerTest extends ControllerTestCase
{
    private $user;

    protected function setUp()
    {
        $this->user = $this->prophesize(User::class)->reveal();
    }

    private function createController()
    {
        return new HomeController();
    }

    public function testIndexAction()
    {
        $view = $this->createController()->indexAction($this->user);
        $this->assertDataInstanceOf($view, DashboardList::class);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE);
    }

    public function testSearchAction()
    {
        $view = $this->createController()->searchAction();
        $this->assertFormView($view, FilterFormType::class, FilterCommand::class);
        $this->assertDataInstanceOf($view, FilterCommand::class);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE);
    }
}