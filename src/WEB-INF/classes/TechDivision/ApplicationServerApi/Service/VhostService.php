<?php

/**
 * TechDivision\ApplicationServerApi\Service\VhostService
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

/**
 *
 * @package TechDivision\ApplicationServerApi
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim <tw@techdivision.com>
 */
class VhostService extends AbstractService
{

    /**
     * Class name of the persistence container proxy that handles the data.
     *
     * @var string
     */
    const SERVICE_CLASS = 'TechDivision\ApplicationServer\Api\VhostService';
    
    /**
     * Returns all container nodes registered in system configuration.
     * 
     * @return \stdClass A \stdClass representation of the container nodes
     */
    public function findAll()
    {

        // load all vhost nodes
        $vhostNodes = $this->getApi(self::SERVICE_CLASS)->findAll();

        // initialize class container
        $stdClass = new \stdClass();
        $stdClass->vhosts = array();
        
        // convert the vhost nodes into stdClass representation
        foreach ($vhostNodes as $vhostNode) {
            $stdClass->vhosts[] = $vhostNode->toStdClass();
        }
        
        // return the stdClass representation of the vhosts
        return $stdClass;
    }
    
    /**
     * Initializes the stdClass representation of the vhost node with
     * the ID passed as parameter.
     * 
     * @param string $id The ID of the requested vhost node
     * @return \stdClass The vhost node as \stdClass representation
     */
    public function load($id)
    {
        
        // load the vhost with the requested ID
        $vhostNode = $this->getApi(self::SERVICE_CLASS)->load($id);

        // initialize a class container
        $stdClass = new \stdClass();
        $stdClass->vhost = $vhostNode->toStdClass();

        // return the stdClass representation of the vhost
        return $stdClass;
    }
}