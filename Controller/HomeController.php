<?php

namespace Cis\EducationalVisitBundle\Controller;

use Cis\EducationalVisitBundle\CommandBus\Home\FilterCommand;
use Cis\EducationalVisitBundle\Form\Home\FilterFormType;
use Cis\EducationalVisitBundle\View\DashboardList;
use Symfony\Component\Security\Core\User\UserInterface;

class HomeController extends Controller
{
    public function indexAction(UserInterface $user)
    {
        return $this
            ->createView()
            ->setData(new DashboardList($user),'dashboard')
	        ->restrictTo(self::ACCESS_RULE)
	    ;
    }
    
    public function searchAction()
    {
        $command = new FilterCommand();
        return $this
            ->createFormView(FilterFormType::class, $command)
            ->setData($command, 'filter')
	        ->restrictTo(self::ACCESS_RULE)
	    ;
    }
}
