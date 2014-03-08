<?php

/**
 * TechDivision\ApplicationServerApi\Servlets\AbstractServlet
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServerApi
 * @subpackage Servlets
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServerApi\Servlets;

use TechDivision\Servlet\ServletConfig;
use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\Servlet\Http\HttpServletResponse;
use TechDivision\ServletEngine\Http\Servlet;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServerApi\Service\Service;

/**
 * Abstract servlet that provides basic functionality for
 * all other API servlets.
 * 
 * @category   Appserver
 * @package    TechDivision_ApplicationServerApi
 * @subpackage Servlets
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractServlet extends Servlet
{

    /**
     * The initial context instance passed from the servlet config.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * API Service wrapper.
     *
     * @var \TechDivision\ApplicationServerApi\Service\Service
     */
    protected $service;

    /**
     * Initializes the servlet when the application server starts.
     *
     * @param \TechDivision\Servlet\ServletConfig $config The servlet configuration
     *
     * @return void
     * @see \TechDivision\Servlet\GenericServlet::init()
     */
    public function init(ServletConfig $config)
    {
        
        // call parent init method
        parent::init($config);
        
        // set the initial context instance
        $this->setInitialContext(
            $this->getServletConfig()->getApplication()->getInitialContext()
        );
    }
    
    /**
     * Set's the actual service instance to use.
     * 
     * @param \TechDivision\ApplicationServerApi\Service\Service $service The service instance to set
     * 
     * @return void
     */
    public function setService(Service $service)
    {
        $this->service = $service;
    }
    
    /**
     * Return's the actual service instance to use.
     * 
     * @return \TechDivision\ApplicationServerApi\Service\Service The requested service instance
     */
    public function getService()
    {
        return $this->service;
    }
    
    /**
     * Generic finder implementation using the actual service instance.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     * 
     * @return void
     * @see \TechDivision\ApplicationServerApi\Service\Service::load();
     * @see \TechDivision\ApplicationServerApi\Service\Service::findAll();
     */
    public function find(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        // load the requested URI
        $uri = trim($servletRequest->getUri(), '/');
        
        // first check if a collection of ID's has been requested
        if ($ids = $servletRequest->getParameter('ids')) {
        
            // load all entities with the passed ID's
            $content = array();
            foreach ($ids as $id) {
                $content[] = $this->getService()->load($id);
            }
            
        } else { // then check if all entities has to be loaded or exactly one
        
            // extract the ID of available, and load the requested OR all entities
            list ($applicationName, $entity, $id) = explode('/', $uri, 3);
            if ($id == null) {
                $content = $this->getService()->findAll();
            } else {
                $content = $this->getService()->load($id);
            }
        }
        
        // set the JSON encoded data in the response
        $servletResponse->addHeader('Content-Type', 'application/json');
        $servletResponse->appendBodyStream(json_encode($content));
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
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext The initial context instance
     * 
     * @return void
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
     * @param \TechDivision\ServletContainer\Http\ServletRequest $servletRequest The request instance
     * 
     * @return string The applications base URL
     */
    public function getBaseUrl(ServletRequest $servletRequest)
    {
        // initialize the base URL
        $baseUrl = '/';
        
        // if the application has NOT been called over a VHost configuration append application folder name
        if (!$this->getServletConfig()->getApplication()->isVhostOf($servletRequest->getServerName())) {
            $baseUrl .= $this->getServletConfig()->getApplication()->getName() . '/';
        }
        
        // return the base URL
        return $baseUrl;
    }
}
