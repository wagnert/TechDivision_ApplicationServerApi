<?php

/**
 * TechDivision\ApplicationServerApi\Servlets\AppServlet
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
use TechDivision\ApplicationServer\Utilities\DirectoryKeys;

/**
 *
 * @package TechDivision\ApplicationServerApi
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim <tw@techdivision.com>
 */
class AppServlet extends AbstractServlet
{

    /**
     * Filename of the uploaded file with the webapp PHAR.
     *
     * @var string
     */
    const UPLOADED_PHAR_FILE = 'file';
    
    /**
     * The service class name to use.
     * 
     * @var string
     */
    const SERVICE_CLASS = '\TechDivision\ApplicationServerApi\Service\AppService';

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
        $this->setService($initialContext->newInstance(AppServlet::SERVICE_CLASS, array(
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
        
        // load the deploy directory
        $deployDirectory = $this->getServletConfig()
            ->getApplication()
            ->getBaseDirectory(DIRECTORY_SEPARATOR . DirectoryKeys::DEPLOY);
        
        // save the uploaded PHAR in the deploy directory
        $part = $req->getPart(AppServlet::UPLOADED_PHAR_FILE);
        file_put_contents($deployDirectory . DIRECTORY_SEPARATOR . $part->getFilename(), $part->getInputStream());
        
        // create a new \stdClass instance
        $application = $this->getInitialContext()->newInstance('\stdClass');
        $application->name = $part->getFilename();
        
        // create a new web app
        $this->getService()->create($application);
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