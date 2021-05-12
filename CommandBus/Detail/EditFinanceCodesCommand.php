<?php

namespace Cis\EducationalVisitBundle\CommandBus\Detail;

use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Component\CommandBus\SelfHandlingCommand;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class EditFinanceCodesCommand extends SelfHandlingCommand
{
    private $visit;
    public $evNumber;
    public $costCode;

    public function __construct(Visit $visit)
    {
        $this->visit = $visit;
        $this->evNumber = $visit->getEvNumber();
        $this->costCode = $visit->getCostCode();
    }

    public function handle()
    {
        $this->visit->addFinanceCodes($this->evNumber, $this->costCode);
    }

    public function getVisit()
    {
        return $this->visit;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('evNumber', new Length([
            'max' => Visit::MAX_LENGTH_EV_NUMBER
        ]));
        $metadata->addPropertyConstraint('costCode', new Length([
            'max' => Visit::MAX_LENGTH_COST_CODE
        ]));
    }
}