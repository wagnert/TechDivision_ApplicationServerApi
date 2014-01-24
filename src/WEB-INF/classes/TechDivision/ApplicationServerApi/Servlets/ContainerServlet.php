<?php

/**
 * TechDivision\ApplicationServerApi\Servlets\ContainerServlet
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
use TechDivision\ApplicationServerApi\Service\ContainerService;

/**
 *
 * @package TechDivision\ApplicationServerApi
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim <tw@techdivision.com>
 */
class ContainerServlet extends AbstractServlet
{
    
    /**
     * The service class name to use.
     * 
     * @var string
     */
    const SERVICE_CLASS = '\TechDivision\ApplicationServerApi\Service\ContainerService';
    
    /**
     * Assmbler to assemble app nodes to stdClass representation.
     *
     * @var \TechDivision\ApplicationServerApi\Service\ContainerService
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
        $this->setService($initialContext->newInstance(ContainerServlet::SERVICE_CLASS, array(
            $initialContext
        )));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ServletContainer\Servlets\HttpServlet::doGet()
     */
    public function doGet(Request $req, Response $res)
    {
        $this->find($req, $res);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ServletContainer\Servlets\HttpServlet::doPost()
     */
    public function doPost(Request $req, Response $res)
    {
        $content = json_decode($req->getContent());
        $this->getService()->create($content);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ServletContainer\Servlets\HttpServlet::doPut()
     */
    public function doPut(Request $req, Response $res)
    {
        $content = json_decode($req->getContent());
        $this->getService()->update($content);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ServletContainer\Servlets\HttpServlet::doDelete()
     */
    public function doDelete(Request $req, Response $res)
    {
        $uri = trim($req->getUri(), '/');
        list ($applicationName, $entity, $id) = explode('/', $uri, 3);
        $this->getService()->delete($id);
    }
}