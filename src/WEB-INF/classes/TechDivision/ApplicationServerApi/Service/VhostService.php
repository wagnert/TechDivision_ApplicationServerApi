<?php

/**
 * TechDivision\ApplicationServerApi\Service\VhostService
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

/**
 * Service implementation that handles all vhost related functionality.
 * 
 * @category   Appserver
 * @package    TechDivision_ApplicationServerApi
 * @subpackage Service
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
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
     * Returns all vhost nodes registered in system configuration.
     *
     * @return \stdClass A \stdClass representation of the vhost nodes
     * @see \TechDivision\ApplicationServerApi\Service\AbstractService::findAll()
     */
    public function findAll()
    {
        
        // load all container nodes
        $containerNodes = $this->getApi(ContainerService::SERVICE_CLASS)->findAll();
        
        // initialize class container
        $stdClass = new \stdClass();
        $stdClass->vhosts = array();
        
        // load the app nodes
        $appNodes = $this->getApi(AppService::SERVICE_CLASS)->findAll();
        
        // convert the vhost nodes into stdClass representation
        foreach ($containerNodes as $containerNode) {
            
            // load the receiver information
            $receiverNode = $containerNode->getReceiver();
            
            // load the host node and iterate over all vhost nodes
            $hostNode = $containerNode->getHost();
            foreach ($hostNode->getVhosts() as $vhostNode) {
                
                // prepare the base directory (the app nodes primary key)
                $appBase = $hostNode->getAppBase() . $vhostNode->getAppBase();
                $baseDirectory = $this->getApi(self::SERVICE_CLASS)->getBaseDirectory($appBase);
                
                // create the vhost stdClass representation
                $vhost = $vhostNode->toStdClass();
                
                // add container name
                $vhost->container_name = $containerNode->getName();
                
                // add address/port number
                $vhost->address = $receiverNode->getParam('address');
                $vhost->port = $receiverNode->getParam('port');
                
                // try to load the vhost's app node
                if (array_key_exists($baseDirectory, $appNodes)) {
                    $vhost->app = $appNodes[$baseDirectory]->getPrimaryKey();
                }
                
                // add the vhost stdClass representation to the array
                $stdClass->vhosts[] = $vhost;
            }
        }
        
        // return the stdClass representation of the vhosts
        return $stdClass;
    }

    /**
     * Initializes the stdClass representation of the vhost node with
     * the ID passed as parameter.
     *
     * @param string $id The ID of the requested vhost node
     * 
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
