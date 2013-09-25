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
use TechDivision\ApplicationServerApi\Servlets\AbstractServlet;

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
     * Class name of the persistence container proxy that handles the data.
     * @var string
     */
    const SERVICE_CLASS = 'TechDivision\ApplicationServer\Api\VhostService';

    /**
     *
     * @param Request $req
     * @param Response $res
     * @throws \Exception
     */
    public function doGet(Request $req, Response $res)
    {

        $res->addHeader('Content-Type', 'application/json');
        $res->setContent(json_encode($content));
    }

    public function doPost(Request $req, Response $res)
    {

        $res->addHeader('Content-Type', 'application/json');
        $res->setContent(json_encode($content));
    }
}