<?php

namespace Cis\EducationalVisitBundle\Tests\CommandBus\Itinerary;

use Cis\EducationalVisitBundle\CommandBus\Itinerary\UploadCommand;
use Cis\EducationalVisitBundle\CommandBus\Itinerary\UploadHandler;
use Cis\EducationalVisitBundle\Entity\Itinerary;
use Cis\EducationalVisitBundle\Entity\Visit;
use Petroc\Bridge\PhpUnit\TestCase;
use Petroc\Component\Helper\FileUploaderInterface;
use Petroc\Component\Helper\Orm;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\File\File;

class UploadHandlerTest extends TestCase
{
    private $visit;
    private $itinerary;
    private $orm;
    private $fileUploader;
    private $file;

    protected function setUp()
    {
        $this->visit = $this->prophesize(Visit::class);
        $this->itinerary = $this->prophesize(Itinerary::class);
        $this->orm = $this->prophesize(Orm::class);
        $this->fileUploader = $this->prophesize(FileUploaderInterface::class);
        $this->file = $this->prophesize(File::class)->reveal();
    }

    public function testHandleNewUpload()
    {
        $file = $this->file;

        $visit = $this->visit;
        $visit->getItinerary()->willReturn(null);
        $visit->setItinerary(Argument::type(Itinerary::class))->shouldBeCalledOnce();
        $visit = $visit->reveal();

        $orm = $this->orm;
        $orm->persist(Argument::type(Itinerary::class))->shouldBeCalledOnce();
        $orm = $orm->reveal();

        $fileUploader = $this->fileUploader;
        $fileUploader->upload(
            Argument::exact(Itinerary::FILE_DIR),
            Argument::exact($file)
        )->shouldBeCalledOnce()->willReturn('/file/path');
        $fileUploader = $fileUploader->reveal();

        $command = new UploadCommand($visit);
        $command->file = $file;
        $handler = new UploadHandler($orm, $fileUploader);

        $handler->handle($command);
    }

    public function testHandleReplaceUpload()
    {
        $file = $this->file;
        $path = '/file/path';

        $itinerary = $this->itinerary;
        $itinerary->getFileLocation()->willReturn($path);
        $itinerary->setFileLocation(Argument::exact($path))->shouldBeCalledOnce();
        $itinerary = $itinerary->reveal();

        $visit = $this->visit;
        $visit->getItinerary()->willReturn($itinerary);
        $visit->setItinerary(Argument::type(Itinerary::class))->shouldNotBeCalled();
        $visit = $visit->reveal();

        $orm = $this->orm;
        $orm->persist(Argument::type(Itinerary::class))->shouldNotBeCalled();
        $orm = $orm->reveal();

        $fileUploader = $this->fileUploader;
        $fileUploader->upload(
            Argument::exact(Itinerary::FILE_DIR),
            Argument::exact($file),
            Argument::exact($path)
        )->shouldBeCalledOnce()->willReturn('/file/path');
        $fileUploader = $fileUploader->reveal();

        $command = new UploadCommand($visit);
        $command->file = $file;
        $handler = new UploadHandler($orm, $fileUploader);

        $handler->handle($command);
    }

    public function testHandleNoFile()
    {
        $itinerary = $this->itinerary->reveal();

        $visit = $this->visit;
        $visit->getItinerary()->willReturn($itinerary);
        $visit->setItinerary(Argument::type(Itinerary::class))->shouldNotBeCalled();
        $visit = $visit->reveal();

        $orm = $this->orm;
        $orm->persist(Argument::type(Itinerary::class))->shouldNotBeCalled();
        $orm = $orm->reveal();

        $fileUploader = $this->fileUploader;
        $fileUploader->upload(
            Argument::exact(Itinerary::FILE_DIR),
            Argument::any(),
            Argument::any()
        )->shouldNotBeCalled();
        $fileUploader = $fileUploader->reveal();

        $command = new UploadCommand($visit);
        $handler = new UploadHandler($orm, $fileUploader);

        $handler->handle($command);
    }
}