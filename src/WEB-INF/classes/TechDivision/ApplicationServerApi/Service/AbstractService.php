<?php

/**
 * TechDivision\ApplicationServerApi\Service\AppService
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServerApi\Service;

use TechDivision\ApplicationServer\InitialContext;

/**
 *
 * @package TechDivision\ApplicationServerApi
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim <tw@techdivision.com>
 */
class AbstractService
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
     * The initial context instance passed from the servlet.
     * 
     * @param InitialContext $initialContext The initial context instance
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
     * 
     * @param string $id
     */
    public function load($id)
    {
        throw new \Exception(__METHOD__ . ' not implemented');
    }
    
    /**
     * 
     */
    public function findAll()
    {
        throw new \Exception(__METHOD__ . ' not implemented');
    }
    
    /**
     * 
     * @param \stdClass $toCreate
     */
    public function create(\stdClass $toCreate)
    {
        throw new \Exception(__METHOD__ . ' not implemented');
    }
    
    /**
     * 
     * @param \stdClass $toUpdate
     */
    public function update(\stdClass $toUpdate)
    {
        throw new \Exception(__METHOD__ . ' not implemented');
    }
    
    /**
     * 
     * @param string $id
     */
    public function delete($id)
    {
        throw new \Exception(__METHOD__ . ' not implemented');
    }
    
    /**
     * Creates a new instance of the passed API class name
     * and returns it.
     *
     * @param string $apiClass
     *            The API class name to return the instance for
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
     * @return void
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
    
    /**
     * Return's the base URL for rendering images/thumbnails.
     * 
     * @return string The base URL
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
}