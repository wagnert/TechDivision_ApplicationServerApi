<?php

/**
 * TechDivision\ApplicationServerApi\Servlets\ThumbnailServlet
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServerApi\Servlets;

use TechDivision\ServletContainer\Interfaces\Request;
use TechDivision\ServletContainer\Interfaces\Response;
use TechDivision\ServletContainer\Interfaces\ServletConfig;
use TechDivision\ApplicationServerApi\Servlets\AbstractServlet;
use TechDivision\ApplicationServerApi\Service\AppService;

/**
 *
 * @package TechDivision\ApplicationServerApi
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim <tw@techdivision.com>
 */
class ThumbnailServlet extends AbstractServlet
{

    /**
     * Assmbler to assemble app nodes to stdClass representation.
     *
     * @var \TechDivision\ApplicationServerApi\Service\AppService
     */
    protected $service;

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ServletContainer\Servlets\GenericServlet::init()
     */
    public function init(ServletConfig $config)
    {
        
        // call parent init method
        parent::init($config);
        
        // create a new service instance 
        $initialContext = $this->getInitialContext();
        $this->service = $initialContext->newInstance('\TechDivision\ApplicationServerApi\Service\AppService', array(
            $initialContext
        ));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ServletContainer\Servlets\HttpServlet::doGet()
     */
    public function doGet(Request $req, Response $res)
    {
        
        // explode the URI
        $uri = trim($req->getUri(), '/');
        list ($applicationName, $entity, $id) = explode('/', $uri, 3);
        
        // set the base URL for rendering images/thumbnails
        $this->service->setBaseUrl($this->getBaseUrl($req));
        $thumbnailPath = $this->service->thumbnail($id);
        
        // check of the file exists, if yes, return the thumbnail image
        if (file_exists($thumbnailPath)) {
            $res->addHeader('Content-Type', 'image/png');
            $res->setContent(file_get_contents($thumbnailPath));
        }
    }
}