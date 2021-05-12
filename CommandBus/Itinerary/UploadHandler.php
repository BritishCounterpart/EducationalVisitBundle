<?php

namespace Cis\EducationalVisitBundle\CommandBus\Itinerary;

use Cis\EducationalVisitBundle\Entity\Itinerary;
use Petroc\Component\CommandBus\HandlerInterface;
use Petroc\Component\Helper\FileUploaderInterface;
use Petroc\Component\Helper\Orm;

class UploadHandler implements HandlerInterface
{
    private $orm;
    private $fileUploader;

    public function __construct(Orm $orm, FileUploaderInterface $fileUploader)
    {
        $this->orm = $orm;
        $this->fileUploader = $fileUploader;
    }

    public function handle(UploadCommand $command)
    {
        $file = $command->file;

        $visit = $command->getVisit();
        $itinerary = $command->getVisit()->getItinerary();

        if (null === $document = $itinerary) {
            $itinerary = new Itinerary(
                $visit,
                $this->fileUploader->upload(
                    Itinerary::FILE_DIR,
                    $file
                )
            );

            $visit->setItinerary($itinerary);
            $this->orm->persist($itinerary);
            return;
        }

        if (null === $file) {
            return;
        }

        $itinerary->setFileLocation($this->fileUploader->upload(
            Itinerary::FILE_DIR,
            $file,
            $itinerary->getFileLocation()
        ));
    }
}