<?php

namespace Cis\EducationalVisitBundle\Controller;

use Cis\EducationalVisitBundle\CommandBus\Itinerary\DeleteCommand;
use Cis\EducationalVisitBundle\CommandBus\Itinerary\UploadCommand;
use Cis\EducationalVisitBundle\Entity\Itinerary;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Form\Itinerary\UploadFormType;
use Petroc\Component\Helper\FileUploaderInterface;
use Petroc\Component\View\FileData;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ItineraryController extends Controller
{
    public function indexAction(Visit $visit)
    {
        return $this
            ->createView()
            ->setData($visit, 'visit')
	        ->restrictTo(self::ACCESS_RULE)
	    ;
    }

    public function uploadAction(Visit $visit)
    {
        return $this
            ->createFormView(UploadFormType::class, new UploadCommand($visit))
            ->setData($visit, 'visit')
            ->onSuccessMessage('Itinerary uploaded.')
            ->onSuccessRoute(self::ROUTE_ITINERARY, $visit)
            ->restrictTo(self::ACCESS_RULE_EDIT, $visit)
        ;
    }

    public function deleteAction(Itinerary $itinerary)
    {
        $visit = $itinerary->getVisit();
        return $this
            ->createCommandView(new DeleteCommand($itinerary))
            ->onSuccessMessage('Itinerary deleted.')
            ->onSuccessRoute(self::ROUTE_ITINERARY, $visit)
            ->restrictTo(self::ACCESS_RULE_EDIT, $visit)
        ;
    }

    public function downloadAction(Itinerary $itinerary, FileUploaderInterface $fileUploader)
    {
        return $this
            ->createView()
            ->restrictTo(self::ACCESS_RULE_ITINERARY_DOWNLOAD)
            ->setData(new FileData(
                $fileUploader->getPath(
                    Itinerary::FILE_DIR,
                    $itinerary->getFileLocation()
                ),
                null,
                ResponseHeaderBag::DISPOSITION_INLINE
            ))
        ;
    }
}
