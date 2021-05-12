<?php

namespace Cis\EducationalVisitBundle\CommandBus\Approval;

use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Messenger\EducationalVisitMessenger;
use Petroc\Component\CommandBus\HandlerInterface;

class ApproveHandler implements HandlerInterface
{
    private $messenger;

    public function __construct(EducationalVisitMessenger $messenger)
    {
        $this->messenger = $messenger;
    }

    public function handle(ApproveCommand $command)
    {
        $visit = $command->getVisit();
        $visit->setStatus(Visit::STATUS_APPROVED);

        $this->messenger->sendVisitApprovedEmail(
            $command->getUser(),
            $visit
        );
    }
}