<?php

/**
 * TechDivision\ApplicationServerApi\Servlets\VhostServlet
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
use TechDivision\ApplicationServerApi\Service\VhostService;

/**
 *
 * @package TechDivision\ApplicationServerApi
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim <tw@techdivision.com>
 */
class VhostServlet extends AbstractServlet
{

    /**
     * Service to to handle vhost nodes.
     *
     * @var \TechDivision\ApplicationServerApi\Service\VhostService
     */
    protected $service;

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ServletContainer\Servlets\GenericServlet::init()
     */
    public function init(ServletConfig $config)
    {
        parent::init($config);
        $initialContext = $this->getInitialContext();
        $this->service = $initialContext->newInstance('\TechDivision\ApplicationServerApi\Service\VhostService', array(
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
                $content[] = $this->service->load($i);
            }
        } else {

            list ($applicationName, $entity, $id) = explode('/', $uri);

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
        $res->addHeader('Content-Type', 'application/json');
        $res->setContent(json_encode($content));
    }
}