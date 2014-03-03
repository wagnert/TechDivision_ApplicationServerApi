<?php

/**
 * TechDivision\ApplicationServerApi\Service\AppService
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerApi
 * @subpackage Service
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
namespace TechDivision\ApplicationServerApi\Service;

use TechDivision\ApplicationServer\InitialContext;

/**
 * Abstract service class that provides some basic service functionality.
 * 
 * @category   Appserver
 * @package    TechDivision_ApplicationServerApi
 * @subpackage Service
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class AbstractService implements Service
{

    /**
     * The initial context instance passed from the servlet.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;
    
    /**
     * The base URL for rendering images/thumbnails.
     * 
     * @var string
     */
    protected $baseUrl;
    
    /**
     * The path to the admin WEB-INF configuration path.
     * 
     * @var string
     */
    protected $configurationPath;
    
    /**
     * The initial context instance passed from the servlet.
     * 
     * @param InitialContext $initialContext The initial context instance
     * 
     * @return void
     */
    public function __construct(InitialContext $initialContext)
    {
        $this->initialContext = $initialContext;
    }

    /**
     * Returns the initial context instance passed with the servlet config.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }
    
    /**
     * Initializes the stdClass representation of the configuration node with
     * the ID passed as parameter.
     *
     * @param string $id The ID of the requested configuration node
     * 
     * @return \stdClass The app node as \stdClass representation
     * @throws \Exception Is thrown if the method has not been implemented
     */
    public function load($id)
    {
        throw new \Exception(__METHOD__ . ' not implemented');
    }

    /**
     * Returns all configuration nodes registered in system configuration.
     *
     * @return \stdClass A \stdClass representation of the configuration nodes
     * @throws \Exception Is thrown if the method has not been implemented
     */
    public function findAll()
    {
        throw new \Exception(__METHOD__ . ' not implemented');
    }
    
    /**
     * Creates a new instance of the passed entity.
     * 
     * @param \stdClass $toCreate The data of the entity to be created
     * 
     * @return void
     * @throws \Exception Is thrown if the method has not been implemented
     */
    public function create(\stdClass $toCreate)
    {
        throw new \Exception(__METHOD__ . ' not implemented');
    }
    
    /**
     * Updates the passed entity.
     * 
     * @param \stdClass $toUpdate The data of the entity to update
     * 
     * @return void
     * @throws \Exception Is thrown if the method has not been implemented
     */
    public function update(\stdClass $toUpdate)
    {
        throw new \Exception(__METHOD__ . ' not implemented');
    }
    
    /**
     * Deletes the configuration node with the passed ID
     * from the system configuration.
     * 
     * @param string $id The ID of the configuration node to delete
     * 
     * @return void
     */
    public function delete($id)
    {
        throw new \Exception(__METHOD__ . ' not implemented');
    }
    
    /**
     * Creates a new instance of the passed API class name
     * and returns it.
     *
     * @param string $apiClass The API class name to return the instance for
     * 
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface The API instance
     */
    public function getApi($apiClass)
    {
        $initialContext = $this->getInitialContext();
        $apiInstance = $initialContext->newInstance($apiClass, array($initialContext));
        return $apiInstance;
    }
    
    /**
     * The base URL for rendering images/thumbnails.
     * 
     * @param string $baseUrl The base URL
     * 
     * @return void
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
    
    /**
     * Returns the base URL for rendering images/thumbnails.
     * 
     * @return string The base URL
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
    
    /**
     * The path to the admin WEB-INF configuration path.
     * 
     * @param string $configurationPath The apps configuration path
     * 
     * @return void
     */
    public function setConfigurationPath($configurationPath)
    {
        $this->configurationPath = $configurationPath;
    }
    
    /**
     * Return's the path to the admin WEB-INF configuration path.
     * 
     * @return string The apps configuration path
     */
    public function getConfigurationPath()
    {
        return $this->configurationPath;
    }
}
