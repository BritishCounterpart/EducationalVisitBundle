<?php

namespace Cis\EducationalVisitBundle\CommandBus\Approval;

use App\Entity\User;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Component\CommandBus\Command;

class ApproveCommand extends Command
{
    private $visit;
    private $user;

    public function __construct(Visit $visit, User $user)
    {
        $this->visit = $visit;
        $this->user = $user;
    }

    public function getVisit()
    {
        return $this->visit;
    }

    public function getUser()
    {
        return $this->user;
    }
}