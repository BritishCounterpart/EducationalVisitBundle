<?php

namespace Cis\EducationalVisitBundle\CommandBus\Itinerary;

use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Component\CommandBus\Command;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class UploadCommand extends Command
{
    private $visit;
    public $file;

    public function __construct(Visit $visit)
    {
        $this->visit = $visit;
    }

    public function getVisit()
    {
        return $this->visit;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('file', new File([
            'maxSize' => '1024k',
            'mimeTypes' => [
                'application/pdf',
                'application/x-pdf',
            ],
            'mimeTypesMessage' => 'Please upload a valid PDF',
        ]));
    }
}