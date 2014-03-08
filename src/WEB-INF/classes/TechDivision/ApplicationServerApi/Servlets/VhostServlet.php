<?php

/**
 * TechDivision\ApplicationServerApi\Servlets\VhostServlet
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
 * Servlet that handles all vhost related requests.
 * 
 * @category   Appserver
 * @package    TechDivision_ApplicationServerApi
 * @subpackage Servlets
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class VhostServlet extends AbstractServlet
{
    
    /**
     * The service class name to use.
     * 
     * @var string
     */
    const SERVICE_CLASS = '\TechDivision\ApplicationServerApi\Service\VhostService';

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
                VhostServlet::SERVICE_CLASS,
                array(
                    $initialContext
                )
            )
        );
    }

    /**
     * Tries to load the requested vhosts and adds them to the response.
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
}
