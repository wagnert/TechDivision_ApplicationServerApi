<?php

/**
 * TechDivision\ApplicationServerApi\Servlets\AppServlet
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
 * Servlet that handles all app related requests.
 * 
 * @category   Appserver
 * @package    TechDivision_ApplicationServerApi
 * @subpackage Servlets
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
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
     * The service class name to use.
     *
     * @var string
     */
    const SERVICE_CLASS = '\TechDivision\ApplicationServerApi\Service\AppService';

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
                AppServlet::SERVICE_CLASS,
                array(
                    $initialContext
                )
            )
        );
    }

    /**
     * Tries to load the requested apps and adds them to the response.
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
     * Creates a new app.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     * 
     * @return void
     * @see \TechDivision\Servlet\Http\HttpServlet::doPost()
     */
    public function doPost(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {
        $this->getService()->upload($servletRequest->getPart(AppServlet::UPLOADED_PHAR_FILE));
    }

    /**
     * Updates the app with the passed content.
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
     * Delete the requested app.
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
