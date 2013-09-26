<?php

/**
 * TechDivision\ApplicationServerApi\Servlets\AbstractServlet
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServerApi\Servlets;

use TechDivision\ServletContainer\Interfaces\Servlet;
use TechDivision\ServletContainer\Servlets\HttpServlet;
use TechDivision\ServletContainer\Interfaces\ServletConfig;
use TechDivision\ServletContainer\Interfaces\Request;
use TechDivision\ServletContainer\Interfaces\Response;
use TechDivision\PersistenceContainerClient\Context\Connection\Factory;

/**
 *
 * @package TechDivision\Example
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
abstract class AbstractServlet extends HttpServlet implements Servlet
{

    /**
     * Servlet context to transfer data between the servlet and the view.
     *
     * @var array
     */
    protected $context = array();

    protected $request;

    protected $response;

    /**
     * Returns the base path to the web app.
     *
     * @return string The base path
     */
    public function getWebappPath()
    {
        return $this->getServletConfig()->getWebappPath();
    }

    /**
     * Attaches the passed data under the also passed key in the servlet context.
     *
     * @param $key string
     *            The key to attach the data under
     * @param $value mixed
     *            The data to be attached
     * @return void
     */
    public function addAttribute($key, $value)
    {
        $this->context[$key] = $value;
    }

    /**
     * Returns the data for the passed key.
     *
     * @param string $key
     *            The key to return the data for
     * @return mixed The requested data
     */
    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->context)) {
            return $this->context[$key];
        }
    }

    /**
     * Creates a new proxy for the passed session bean class name
     * and returns it.
     *
     * @param string $serviceClass
     *            The session bean class name to return the proxy for
     * @return mixed The proxy instance
     */
    public function getService($serviceClass)
    {
        $initialContext = $this->getServletConfig()->getApplication()->getInitialContext();
        return $initialContext->newInstance($serviceClass, array($initialContext));
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Returns baseurl for html base tag
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $baseUrl = '/';
        // if the application has NOT been called over a VHost configuration append application folder naem
        if (! $this->getServletConfig()
            ->getApplication()
            ->isVhostOf($this->getRequest()
            ->getServerName())) {
            $baseUrl .= $this->getServletConfig()
                ->getApplication()
                ->getName() . '/';
        }
        return $baseUrl;
    }
}