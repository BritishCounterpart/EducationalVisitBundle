<?php

namespace Cis\EducationalVisitBundle\Tests\Controller;

use Cis\EducationalVisitBundle\CommandBus\Itinerary\DeleteCommand;
use Cis\EducationalVisitBundle\CommandBus\Itinerary\UploadCommand;
use Cis\EducationalVisitBundle\Controller\Controller;
use Cis\EducationalVisitBundle\Controller\ItineraryController;
use Cis\EducationalVisitBundle\Entity\Itinerary;
use Cis\EducationalVisitBundle\Entity\Visit;
use Cis\EducationalVisitBundle\Form\Itinerary\UploadFormType;
use Petroc\Bridge\PhpUnit\ControllerTestCase;
use Petroc\Component\Helper\FileUploaderInterface;
use Petroc\Component\View\FileData;

class ItineraryControllerTest extends ControllerTestCase
{
    private $visit;
    private $itinerary;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class)->reveal();
        $itinerary = $this->prophesize(Itinerary::class);
        $itinerary->getVisit()->willReturn($this->visit);
        $itinerary->getFileLocation()->willReturn('//');
        $this->itinerary = $itinerary->reveal();
    }

    private function createController()
    {
        return new ItineraryController();
    }

    public function testIndexAction()
    {
        $visit = $this->visit;
        $view = $this->createController()->indexAction($visit);
        $this->assertDataSame($view, $visit);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE);
    }

    public function testUploadAction()
    {
        $visit = $this->visit;
        $view = $this->createController()->uploadAction($visit);
        $this->assertCommandFormView($view, UploadFormType::class, UploadCommand::class);
        $this->assertDataSame($view, $visit);
        $this->assertSuccessRoute($view, Controller::ROUTE_ITINERARY, $visit);
        $this->assertSuccessMessage($view, 'Itinerary uploaded.');
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_EDIT, $visit);
    }

    public function testDeleteAction()
    {
        $itinerary = $this->itinerary;
        $visit = $this->visit;
        $view = $this->createController()->deleteAction($itinerary);
        $this->assertCommandView($view, DeleteCommand::class);
        $this->assertSuccessRoute($view, Controller::ROUTE_ITINERARY, $visit);
        $this->assertSuccessMessage($view, 'Itinerary deleted.');
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_EDIT, $visit);
    }

    public function testDownloadAction()
    {
        $fileUploader = $this->prophesize(FileUploaderInterface::class)->reveal();
        $itinerary = $this->itinerary;
        $view = $this->createController()->downloadAction($itinerary, $fileUploader);
        $this->assertDataInstanceOf($view, FileData::class);
        $this->assertRestrictTo($view, Controller::ACCESS_RULE_ITINERARY_DOWNLOAD);
    }
}