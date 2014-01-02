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
use TechDivision\ApplicationServer\InitialContext;

/**
 *
 * @package TechDivision\ApplicationServerApi
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
abstract class AbstractServlet extends HttpServlet implements Servlet
{

    /**
     * The actual request instance.
     * 
     * @var \TechDivision\ServletContainer\Interfaces\Request
     */
    protected $request;

    /**
     * The actual response instance.
     * 
     * @var \TechDivision\ServletContainer\Interfaces\Response
     */
    protected $response;

    /**
     * The initial context instance passed from the servlet config.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ServletContainer\Servlets\GenericServlet::init()
     */
    public function init(ServletConfig $config)
    {
        parent::init($config);
        $this->setInitialContext($this->getServletConfig()
            ->getApplication()
            ->getInitialContext());
    }

    /**
     * Returns the initial context instance passed with the servlet config.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Sets the initial context instance.
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext
     *            The initial context instance
     */
    public function setInitialContext(InitialContext $initialContext)
    {
        $this->initialContext = $initialContext;
    }

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
     * Sets the actual request instance.
     *
     * @param \TechDivision\ServletContainer\Interfaces\Request $request
     *            The request instance to set
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Sets the actual response instance.
     *
     * @param \TechDivision\ServletContainer\Interfaces\Response $response
     *            The response instance to set
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Returns the actual request instance.
     *
     * @return \TechDivision\ServletContainer\Interfaces\Request The request instance
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the actual response instance.
     *
     * @return \TechDivision\ServletContainer\Interfaces\Response The response instance
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Returns the application's base URL for html base tag
     *
     * @return string The application's base URL
     */
    public function getBaseUrl()
    {
        // initialize the base URL
        $baseUrl = '/';
        
        // if the application has NOT been called over a VHost configuration append application folder name
        if (! $this->getServletConfig()
            ->getApplication()
            ->isVhostOf($this->getRequest()
            ->getServerName())) {
            $baseUrl .= $this->getServletConfig()
                ->getApplication()
                ->getName() . '/';
        }
        
        // return the base URL
        return $baseUrl;
    }
}