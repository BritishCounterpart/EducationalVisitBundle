<?php

namespace Cis\EducationalVisitBundle\CommandBus\Approval;

use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Messenger\EducationalVisitMessenger;
use Petroc\Component\CommandBus\HandlerInterface;

class RejectHandler implements HandlerInterface
{
    private $messenger;

    public function __construct(EducationalVisitMessenger $messenger)
    {
        $this->messenger = $messenger;
    }

    public function handle(RejectCommand $command)
    {
        $visit = $command->getVisit();
        $visit->setStatus(Visit::STATUS_NOT_APPROVED);

        $this->messenger->sendVisitRejectedEmail(
            $command->getUser(),
            $visit,
            $command->reason
        );
    }
}