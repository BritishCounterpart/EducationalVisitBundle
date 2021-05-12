<?php

namespace Cis\EducationalVisitBundle\CommandBus\Payment;

use App\Util\OrderUtil;
use Petroc\Component\CommandBus\HandlerInterface;
use DateTime;

class TransferPaymentToStudentParticipantHandler implements HandlerInterface
{
    private $util;

    public function __construct(OrderUtil $util)
    {
        $this->util = $util;
    }

    public function handle(TransferPaymentToStudentParticipantCommand $command)
    {
        $fromStudentParticipant = $command->getFromStudentParticipant();
        $fromVisit = $fromStudentParticipant->getVisit();
        $toStudentParticipant = $command->getToStudentParticipant();
        $toVisit = $toStudentParticipant->getVisit();

        $date = new DateTime;
        $date = $date->format('d-m-Y H:i:s');

        $description = $date.' - Transferred '. $fromVisit->getTitle(). ' To '. $toVisit->getTitle();

        $options = [
            'description' => $description,
            'from_object' => $fromStudentParticipant->getId(),
            'from_reference' => $fromVisit->getEvNumber(),
            'from_cost_code' => $fromVisit->getCostCode(),
            'to_object' => $toStudentParticipant->getId(),
            'to_reference' => $toVisit->getEvNumber(),
            'to_cost_code' => $toVisit->getCostCode()
        ];

        $this->util->transferOrder($options);
    }
}