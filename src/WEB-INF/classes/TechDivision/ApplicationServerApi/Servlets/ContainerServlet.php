<?php

/**
 * TechDivision\ApplicationServerApi\Servlets\ContainerServlet
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

/**
 * Servlet that handles all container related requests.
 * 
 * @category   Appserver
 * @package    TechDivision_ApplicationServerApi
 * @subpackage Servlets
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ContainerServlet extends AbstractServlet
{
    
    /**
     * The service class name to use.
     * 
     * @var string
     */
    const SERVICE_CLASS = '\TechDivision\ApplicationServerApi\Service\ContainerService';
    
    /**
     * Assmbler to assemble app nodes to stdClass representation.
     *
     * @var \TechDivision\ApplicationServerApi\Service\ContainerService
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
        
        // create a new service instance
        $initialContext = $this->getInitialContext();
        $this->setService(
            $initialContext->newInstance(
                ContainerServlet::SERVICE_CLASS,
                array(
                    $initialContext
                )
            )
        );
    }

    /**
     * Tries to load the requested containers and adds them to the response.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     * 
     * @return void
     * @see \TechDivision\Servlet\Http\HttpServlet::doGet()
     */
    public function doGet(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {
        $this->find($servletRequest, $servletResponse);
    }

    /**
     * Creates a new container.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     * 
     * @return void
     * @see \TechDivision\Servlet\Http\HttpServlet::doPost()
     */
    public function doPost(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {
        $content = json_decode($servletRequest->getContent());
        $this->getService()->create($content);
    }

    /**
     * Updates the container with the passed content.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     * 
     * @return void
     * @see \TechDivision\Servlet\Http\HttpServlet::doPut()
     */
    public function doPut(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {
        $content = json_decode($servletRequest->getContent());
        $this->getService()->update($content);
    }

    /**
     * Delete the requested container.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     * 
     * @return void
     * @see \TechDivision\Servlet\Http\HttpServlet::doDelete()
     */
    public function doDelete(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {
        $uri = trim($servletRequest->getUri(), '/');
        list ($applicationName, $entity, $id) = explode('/', $uri, 3);
        $this->getService()->delete($id);
    }
}
