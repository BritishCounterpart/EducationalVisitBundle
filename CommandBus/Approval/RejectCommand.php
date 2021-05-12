<?php

namespace Cis\EducationalVisitBundle\CommandBus\Approval;

use App\Entity\User;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Component\CommandBus\Command;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class RejectCommand extends Command
{
    private $visit;
    private $user;
    public $reason;

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

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('reason', new NotBlank());
    }
}