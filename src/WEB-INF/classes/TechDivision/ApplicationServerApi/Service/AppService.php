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

use TechDivision\ApplicationServer\Api\Node\AppNode;
use TechDivision\ServletContainer\Http\HttpPart;

/**
 * Service implementation that handles all app related functionality.
 * 
 * @category   Appserver
 * @package    TechDivision_ApplicationServerApi
 * @subpackage Service
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
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
     * The thumbnail image name.
     * 
     * @var string
     */
    const THUMBNAIL = 'app-thumbnail.png';
    
    /**
     * The thumbnail placeholder image name.
     * 
     * @var string
     */
    const THUMBNAIL_PLACEHOLDER = 'app-placeholder-300x200.png';

    /**
     * Returns all app nodes registered in system configuration.
     *
     * @return \stdClass A \stdClass representation of the app nodes
     * @see \TechDivision\ApplicationServerApi\Service\AbstractService::findAll()
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
     * Returns the app with the passed ID from the system configuration.
     * 
     * @param string $id The ID of the app node to be returned
     * 
     * @return \stdClass A \stdClass representation of the app node
     * @see \TechDivision\ApplicationServerApi\Service\AbstractService::load()
     */
    public function load($id)
    {
        
        // load the application with the requested ID
        $appNode = $this->getApi(self::SERVICE_CLASS)->load($id);
        
        // initialize a class container
        $stdClass = new \stdClass();
        $stdClass->app = $appNode->toStdClass();
        
        // load the container nodes and append them
        $stdClass->containers = array();
        $containerNodes = $this->getApi(ContainerService::SERVICE_CLASS)->findAll();
        foreach ($containerNodes as $containerNode) {
            if (strstr($appNode->getWebappPath(), $containerNode->getHost()->getAppBase())) {
                $stdClass->containers[] = $containerNode->toStdClass();
            }
        }
        
        // return the stdClass representation of the app
        return $stdClass;
    }
    
    /**
     * Uploads the passed file to the application servers deploy directory.
     *  
     * @param \TechDivision\ServletContainer\Http\HttpPart $part The file data
     * 
     * @return void
     */
    public function upload(HttpPart $part)
    {
        
        // prepare service instance
        $api = $this->getApi(self::SERVICE_CLASS);
        
        // prepare the upload target in the deploy directory
        $target = $api->getTmpDir() . DIRECTORY_SEPARATOR . $part->getFilename();
        
        // save the uploaded file in the tmp directory
        file_put_contents($target, $part->getInputStream());
        
        // let the API soak the archive
        $api->soak(new \SplFileInfo($target));
    }
    
    /**
     * Deletes the app node with the passed ID from the system configuration.
     * 
     * @param string $id The ID of the app node to delete
     * 
     * @return void
     * @see \TechDivision\ApplicationServerApi\Service\AbstractService::delete()
     */
    public function delete($id)
    {
        $this->getApi(self::SERVICE_CLASS)->undeploy($id);
    }
    
    /**
     * Returns the path to the thumbnail image of the app with the 
     * passed ID.
     * 
     * @param string $id ID of the app to return the thumbnail for
     * 
     * @return string The absolute path the thumbnail
     */
    public function thumbnail($id)
    {
        
        // load the application with the requested ID
        $appNode = $this->getApi(self::SERVICE_CLASS)->load($id);
        
        // return the thumbnail path for the admin interface
        return $this->getThumbnailPath($appNode);
    }

    /**
     * Returns the full path to the app's thumbnail.
     *
     * @param AppNode $appNode The app node to return the thumbnail path for
     * 
     * @return string The absolute path to the app's thumbnail
     */
    protected function getThumbnailPath(AppNode $appNode)
    {
        
        // prepare the thumbnail path of the passed app node
        $thumbnailPath = $appNode->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . self::THUMBNAIL;
        
        // check if the app contains a thumbnail in it's WEB-INF folder
        if (file_exists($thumbnailPath)) {
            return $thumbnailPath;
        }
        
        // if not, return the placeholder thumbnail
        return $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF'  . DIRECTORY_SEPARATOR . self::THUMBNAIL_PLACEHOLDER;
    }
}
