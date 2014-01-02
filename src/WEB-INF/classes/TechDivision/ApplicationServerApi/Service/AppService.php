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

use TechDivision\ServletContainer\Interfaces\Request;
use TechDivision\ServletContainer\Interfaces\Response;
use TechDivision\ApplicationServer\Api\Node\NodeInterface;
use TechDivision\ApplicationServerApi\Service\AbstractService;
use TechDivision\ApplicationServerApi\Service\ContainerService;

/**
 *
 * @package TechDivision\ApplicationServerApi
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim <tw@techdivision.com>
 */
class AppService extends AbstractService
{
    
    /**
     * Class name of the persistence container proxy that handles the data.
     *
     * @var string
     */
    const SERVICE_CLASS = 'TechDivision\ApplicationServer\Api\AppService';
    
    /**
     * Returns all app nodes registered in system configuration.
     * 
     * @return \stdClass A \stdClass representation of the app nodes
     */
    public function findAll()
    {

        // load all application nodes
        $appNodes = $this->getApi(self::SERVICE_CLASS)->findAll();
        
        // initialize class container
        $stdClass = new \stdClass();
        $stdClass->apps = array();
        
        // convert the application nodes into stdClass representation
        foreach ($appNodes as $appNode) {
            $stdClass->apps[] = $appNode->toStdClass();
        }
        
        // return the stdClass representation of the apps
        return $stdClass;
    }
    
    /**
     * Initializes the stdClass representation of the app node with
     * the ID passed as parameter.
     * 
     * @param string $id The ID of the requested app node
     * @return \stdClass The app node as \stdClass representation
     */
    public function load($id)
    {
        
        // load the application with the requested ID
        $appNode = $this->getApi(self::SERVICE_CLASS)->load($id);
        
        // initialize a class container
        $stdClass = new \stdClass();
        $stdClass->app = $appNode->toStdClass();
        $stdClass->containers = array();
        
        // load the container nodes and append them
        $containerNodes = $this->getApi(ContainerService::SERVICE_CLASS)->findAll();
        foreach ($containerNodes as $containerNode) {
            if (strstr($appNode->getWebappPath(), $containerNode->getHost()->getAppBase())) {
                $stdClass->containers[] = $containerNode->toStdClass();
            }
        }
        
        // return the stdClass representation of the app
        return $stdClass;
    }
}