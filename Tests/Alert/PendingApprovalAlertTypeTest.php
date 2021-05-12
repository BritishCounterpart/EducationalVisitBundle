<?php

namespace Cis\EducationalVisitBundle\Tests\Alert;

use App\Alert\AlertBuilder;
use App\Entity\User;
use Cis\EducationalVisitBundle\Alert\PendingApprovalAlertType;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Repository\VisitRepository;
use Petroc\Bridge\PhpUnit\TestCase;
use Prophecy\Argument;

class PendingApprovalAlertTypeTest extends TestCase
{
    private function createAlert()
    {
        return new PendingApprovalAlertType();
    }

    private function createUser()
    {
        return $this->prophesize(User::class);
    }

    private function createVisit()
    {
        return $this->prophesize(Visit::class)->reveal();
    }

    private function createBuilder(User $user, $isStaff = true)
    {
        $builder = $this->prophesize(AlertBuilder::class);
        $builder->getUser()->willReturn($user);
        $builder->isStaff()->willReturn($isStaff);
        return $builder;
    }

    public function testBuildAlertNotStaff()
    {
        $user = $this->createUser()->reveal();
        $builder = $builder = $this->createBuilder($user, false);

        $builder->add(
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any()
        )->shouldNotBeCalled();

        $alert = $this->createAlert();
        $alert->buildAlert($builder->reveal());
    }

    public function testBuildAlert()
    {
        $path = '/path/';
        $user = $this->createUser()->reveal();
        $builder = $builder = $this->createBuilder($user);

        $repo = $this->prophesize(VisitRepository::class);
        $repo
            ->findPendingApprovalVisitByApprover(
                $user
            )
            ->willReturn([
                $this->createVisit(),
                $this->createVisit()
            ])
            ->shouldBeCalled()
        ;

        $builder->getRepository(Visit::class)->willReturn($repo->reveal());

        $builder->generatePath('cis_educational_visit')->willReturn($path);

        $builder->add(
            Argument::exact('Educational Visits Pending Approval'),
            $path,
            1,
            2
        )->shouldBeCalled();

        $alert = $this->createAlert();
        $alert->buildAlert($builder->reveal());
    }

    public function testBuildAlertNoAlerts()
    {
        $user = $this->createUser()->reveal();
        $builder = $builder = $this->createBuilder($user);

        $repo = $this->prophesize(VisitRepository::class);
        $repo
            ->findPendingApprovalVisitByApprover(
                $user
            )
            ->willReturn([])
            ->shouldBeCalled()
        ;

        $builder->getRepository(Visit::class)->willReturn($repo->reveal());

        $builder->add(
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any()
        )->shouldNotBeCalled();

        $alert = $this->createAlert();
        $alert->buildAlert($builder->reveal());
    }

}