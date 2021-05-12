<?php

namespace Cis\EducationalVisitBundle\Tests\DependencyInjection;

use Cis\EducationalVisitBundle\DependencyInjection\CisEducationalVisitExtension;
use Petroc\Bridge\PhpUnit\DependencyInjection\ExtensionTestCase;

class CisEducationalVisitExtensionTest extends ExtensionTestCase
{
    public function testLoad()
    {
        $builder = $this->createContainerBuilder();
        $builder->setParameter('kernel.project_dir', __DIR__ . '/../../../../../');
        $ext = new CisEducationalVisitExtension();
        $ext->load([], $builder);
        $this->assertGreaterThan(0, $builder->getDefinitions());
    }
}
