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
        $uri = trim($req->getUri(), '/');
        
        if ($ids = $req->getParameter('ids')) {
            
            $content = array();
            
            foreach ($ids as $id) {
                $content[] = $this->service->load($id);
            }
            
        } else {
        
            list ($applicationName, $entity, $id) = explode('/', $uri, 3);
            
            if ($id == null) {
                $content = $this->service->findAll();
            } else {
                $content = $this->service->load($id);
            }
        }
        
        $res->addHeader('Content-Type', 'application/json');
        $res->setContent(json_encode($content));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ServletContainer\Servlets\HttpServlet::doPost()
     */
    public function doPost(Request $req, Response $res)
    {
        
        // load the deploy directory
        $deployDirectory = $this->getServletConfig()->getApplication()->getBaseDirectory(DIRECTORY_SEPARATOR . DirectoryKeys::DEPLOY);
        
        // save the uploaded PHAR in the deploy directory
        $part = $req->getPart(self::UPLOADED_PHAR_FILE);
        file_put_contents($deployDirectory . DIRECTORY_SEPARATOR . $part->getFilename(), $part->getInputStream());
        
        $application = new \stdClass();
        $application->name = $part->getFilename();
        
        $this->service->create($application);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ServletContainer\Servlets\HttpServlet::doPut()
     */
    public function doPut(Request $req, Response $res)
    {
        $content = json_decode($req->getContent());
        $this->service->update($content);
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
        $this->service->delete($id);
    }
}