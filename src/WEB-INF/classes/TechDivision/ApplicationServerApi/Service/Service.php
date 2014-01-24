<?php

/**
 * TechDivision\ApplicationServerApi\Service\Service
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServerApi\Service;

/**
 * Service interface for all API services.
 * 
 * @package TechDivision\ApplicationServerApi
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim <tw@techdivision.com>
 */
interface Service
{

    /**
     * Returns the initial context instance passed with the servlet config.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext();
    
    /**
     * Initializes the stdClass representation of the configuration node with
     * the ID passed as parameter.
     *
     * @param string $id
     *            The ID of the requested configuration node
     * @return \stdClass The app node as \stdClass representation
     */
    public function load($id);

    /**
     * Returns all configuration nodes registered in system configuration.
     *
     * @return \stdClass A \stdClass representation of the configuration nodes
     */
    public function findAll();
    
    /**
     * Creates a new configuration node with the data of the
     * passed \stdClass instance.
     * 
     * @param \stdClass $toCreate The data to use for configuration node creation
     * @return void
     */
    public function create(\stdClass $toCreate);
    
    /**
     * Updates The configuration node with the passed data.
     * 
     * @param \stdClass $toUpdate The data to update the configuration node with
     * @return void 
     */
    public function update(\stdClass $toUpdate);
    
    /**
     * Deletes the configuration node with the passed ID
     * from the system configuration.
     * 
     * @param string $id The ID of the configuration node to delete
     * @return void
     */
    public function delete($id);
    
    /**
     * Creates a new instance of the passed API class name
     * and returns it.
     *
     * @param string $apiClass
     *            The API class name to return the instance for
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface The API instance
     */
    public function getApi($apiClass);
    
    /**
     * Return's the base URL for rendering images/thumbnails.
     * 
     * @return string The base URL
     */
    public function getBaseUrl();
    
    /**
     * Return's the path to the admin WEB-INF configuration path.
     * 
     * @return string The app's configuration path
     */
    public function getConfigurationPath();
}