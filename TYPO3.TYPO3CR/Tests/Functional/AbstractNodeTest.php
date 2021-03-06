<?php
namespace TYPO3\TYPO3CR\Tests\Functional;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.TYPO3CR".         *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */
use TYPO3\TYPO3CR\Domain\Repository\WorkspaceRepository;
use TYPO3\TYPO3CR\Domain\Model\Workspace;

/**
 * Base test case for nodes
 */
abstract class AbstractNodeTest extends \TYPO3\Flow\Tests\FunctionalTestCase
{
    /**
     * @var boolean
     */
    protected static $testablePersistenceEnabled = true;

    /**
     * If enabled, this test case will modify the behavior of the security framework
     * in a way which allows for easy simulation of roles and authentication.
     *
     * Note: this will implicitly enable testable HTTP as well.
     *
     * @var boolean
     * @api
     */
    protected $testableSecurityEnabled = true;

    /**
     * @var string the Nodes fixture
     */
    protected $fixtureFileName;

    /**
     * @var string the context path of the node to load initially
     */
    protected $nodeContextPath = '/sites/example/home';

    /**
     * @var \TYPO3\TYPO3CR\Domain\Model\NodeInterface
     */
    protected $node;

    /**
     * @var \TYPO3\TYPO3CR\Domain\Service\ContextFactoryInterface
     */
    protected $contextFactory;

    /**
     * @var WorkspaceRepository
     */
    protected $workspaceRepository;

    /**
     * @var Workspace
     */
    protected $liveWorkspace;

    /**
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->fixtureFileName = __DIR__ . '/Fixtures/NodeStructure.xml';
    }

    public function setUp()
    {
        parent::setUp();
        $this->markSkippedIfNodeTypesPackageIsNotInstalled();

        if ($this->liveWorkspace === null) {
            $this->liveWorkspace = new Workspace('live');
            $this->workspaceRepository = $this->objectManager->get('TYPO3\TYPO3CR\Domain\Repository\WorkspaceRepository');
            $this->workspaceRepository->add($this->liveWorkspace);
            $this->workspaceRepository->add(new Workspace('test', $this->liveWorkspace));
            $this->persistenceManager->persistAll();
        }

        $this->contextFactory = $this->objectManager->get('TYPO3\TYPO3CR\Domain\Service\ContextFactoryInterface');
        $contentContext = $this->contextFactory->create(array('workspaceName' => 'live'));
        $siteImportService = $this->objectManager->get('TYPO3\Neos\Domain\Service\SiteImportService');
        $siteImportService->importFromFile($this->fixtureFileName, $contentContext);
        $this->persistenceManager->persistAll();

        if ($this->nodeContextPath !== null) {
            $this->node = $this->getNodeWithContextPath($this->nodeContextPath);
        }
    }

    /**
     * Retrieve a node through the property mapper
     *
     * @param $contextPath
     * @return \TYPO3\TYPO3CR\Domain\Model\NodeInterface
     */
    protected function getNodeWithContextPath($contextPath)
    {
        /* @var $propertyMapper \TYPO3\Flow\Property\PropertyMapper */
        $propertyMapper = $this->objectManager->get('TYPO3\Flow\Property\PropertyMapper');
        $node = $propertyMapper->convert($contextPath, 'TYPO3\TYPO3CR\Domain\Model\Node');
        $this->assertFalse($propertyMapper->getMessages()->hasErrors(), 'There were errors converting ' . $contextPath);
        return $node;
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->inject($this->contextFactory, 'contextInstances', array());
    }

    protected function markSkippedIfNodeTypesPackageIsNotInstalled()
    {
        $packageManager = $this->objectManager->get('TYPO3\Flow\Package\PackageManagerInterface');
        if (!$packageManager->isPackageActive('TYPO3.Neos.NodeTypes')) {
            $this->markTestSkipped('This test needs the TYPO3.Neos.NodeTypes package.');
        }
    }
}
