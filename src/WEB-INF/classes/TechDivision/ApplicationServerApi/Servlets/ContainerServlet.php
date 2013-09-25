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
     * @var string
     */
    const SERVICE_CLASS = 'TechDivision\ApplicationServer\Api\ContainerService';

    /**
     *
     * @param Request $req
     * @param Response $res
     * @throws \Exception
     */
    public function doGet(Request $req, Response $res)
    {

        $uri = trim($req->getUri(), '/');

        error_log("Found URI: $uri");

        list ($applicationName, $entity, $id) = explode('/', $uri);

        if ($id == null) {
            $content = $this->getService(self::SERVICE_CLASS)->findAll();
        } else {
            $content = $this->getService(self::SERVICE_CLASS)->load($id);
        }

        error_log(var_export($content, true));

        $res->addHeader('Content-Type', 'application/json');
        $res->setContent(json_encode($content));
    }

    public function doPost(Request $req, Response $res)
    {

        $uri = trim($req->getUri(), '/');
        $content = json_decode($req->getContent());

        error_log("Found URI: $uri");

        $this->getService(self::SERVICE_CLASS)->create($content);
    }

    public function doPut(Request $req, Response $res)
    {

        $uri = trim($req->getUri(), '/');
        $content = json_decode($req->getContent());

        error_log("Found URI: $uri");

        $this->getService(self::SERVICE_CLASS)->update($content);
    }

    public function doDelete(Request $req, Response $res)
    {

        $uri = trim($req->getUri(), '/');

        error_log("Found URI: $uri");

        list ($applicationName, $entity, $id) = explode('/', $uri);

        $this->getService(self::SERVICE_CLASS)->delete($id);
    }
}