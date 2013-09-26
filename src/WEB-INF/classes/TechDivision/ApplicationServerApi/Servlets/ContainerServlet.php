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
use TechDivision\ApplicationServerApi\Servlets\AbstractServlet;

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
     * Class name of the persistence container proxy that handles the data.
     *
     * @var string
     */
    const SERVICE_CLASS = 'TechDivision\ApplicationServer\Api\ContainerService';

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
                $content[] = $this->getService(self::SERVICE_CLASS)->load($i);
            }
        } else {

            list ($applicationName, $entity, $id) = explode('/', $uri);

            if ($id == null) {
                $content = $this->getService(self::SERVICE_CLASS)->findAll();
            } else {
                $content = $this->getService(self::SERVICE_CLASS)->load($id);
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
        $uri = trim($req->getUri(), '/');
        $content = json_decode($req->getContent());
        $this->getService(self::SERVICE_CLASS)->create($content);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ServletContainer\Servlets\HttpServlet::doPut()
     */
    public function doPut(Request $req, Response $res)
    {
        $uri = trim($req->getUri(), '/');
        $content = json_decode($req->getContent());
        $this->getService(self::SERVICE_CLASS)->update($content);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ServletContainer\Servlets\HttpServlet::doDelete()
     */
    public function doDelete(Request $req, Response $res)
    {
        $uri = trim($req->getUri(), '/');
        list ($applicationName, $entity, $id) = explode('/', $uri);
        $this->getService(self::SERVICE_CLASS)->delete($id);
    }
}