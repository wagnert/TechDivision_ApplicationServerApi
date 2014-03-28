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

use TechDivision\Http\HttpProtocol;
use TechDivision\Servlet\ServletConfig;
use TechDivision\Servlet\Http\HttpServlet;
use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\Servlet\Http\HttpServletResponse;
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
abstract class AbstractServlet extends HttpServlet
{

    /**
     * Returns the servlets service class to use.
     *
     * @return string The servlets service class
     */
    abstract public function getServiceClass();

    /**
     * Returns the actual service instance to use.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest $servletRequest The request instance
     *
     * @return \TechDivision\ApplicationServerApi\Service\Service The requested service instance
     */
    public function getService(HttpServletRequest $servletRequest)
    {
        $service = $servletRequest->getContext()->newService($this->getServiceClass());
        $service->setWebappPath($servletRequest->getContext()->getWebappPath());
        return $service;
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
                $content[] = $this->getService($servletRequest)->load($id);
            }

        } else { // then check if all entities has to be loaded or exactly one

            // extract the ID of available, and load the requested OR all entities
            list ($applicationName, $entity, $id) = explode('/', $uri, 3);
            if ($id == null) {
                $content = $this->getService($servletRequest)->findAll();
            } else {
                $content = $this->getService($servletRequest)->load($id);
            }
        }

        // set the JSON encoded data in the response
        $servletResponse->addHeader(HttpProtocol::HEADER_CONTENT_TYPE, 'application/json');
        $servletResponse->appendBodyStream(json_encode($content));
    }

    /**
     * Returns the application's base URL for html base tag
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest $servletRequest The request instance
     *
     * @return string The applications base URL
     */
    public function getBaseUrl(HttpServletRequest $servletRequest)
    {
        // initialize the base URL
        $baseUrl = '/';

        // if the application has NOT been called over a VHost configuration append application folder name
        if (!$servletRequest->getContext()->isVhostOf($servletRequest->getServerName())) {
            $baseUrl .= $servletRequest->getContext()->getName() . '/';
        }

        // return the base URL
        return $baseUrl;
    }
}
