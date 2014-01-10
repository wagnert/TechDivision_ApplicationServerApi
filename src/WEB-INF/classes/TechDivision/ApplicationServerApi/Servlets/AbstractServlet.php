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
     * Returns the absolute path to the WEB-INF configuration folder.
     * 
     * @return string The path to the configuration folder
     */
    public function getConfigurationPath()
    {
        return $this->getWebappPath() . DIRECTORY_SEPARATOR . 'WEB-INF';
    }
    
    /**
     * Returns the application's base URL for html base tag
     *
     * @return string The application's base URL
     */
    public function getBaseUrl(Request $req)
    {
        // initialize the base URL
        $baseUrl = '/';
        
        // if the application has NOT been called over a VHost configuration append application folder name
        if (! $this->getServletConfig()
            ->getApplication()
            ->isVhostOf($req->getServerName())) {
            $baseUrl .= $this->getServletConfig()
                ->getApplication()
                ->getName() . '/';
        }
        
        // return the base URL
        return $baseUrl;
    }
}