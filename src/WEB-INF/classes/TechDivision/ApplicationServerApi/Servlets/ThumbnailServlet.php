<?php

/**
 * TechDivision\ApplicationServerApi\Servlets\ThumbnailServlet
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
use TechDivision\WebServer\Dictionaries\MimeTypes;
use TechDivision\ApplicationServerApi\Service\AppService;
use TechDivision\ApplicationServerApi\Servlets\AbstractServlet;
use TechDivision\ApplicationServerApi\Exceptions\FileNotFoundException;
use TechDivision\ApplicationServerApi\Exceptions\FileNotReadableException;
use TechDivision\ApplicationServerApi\Exceptions\FoundDirInsteadOfFileException;

/**
 * Servlet that handles all thumbnail related requests.
 * 
 * @category   Appserver
 * @package    TechDivision_ApplicationServerApi
 * @subpackage Servlets
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ThumbnailServlet extends AbstractServlet
{
    
    /**
     * The service class name to use.
     * 
     * @var string
     */
    const SERVICE_CLASS = '\TechDivision\ApplicationServerApi\Service\AppService';

    /**
     * Returns the servlets service class to use.
     * 
     * @return string The servlets service class
     */
    public function getServiceClass()
    {
        return ThumbnailServlet::SERVICE_CLASS;
    }

    /**
     * Tries to load the requested thumbnail from the applications WEB-INF directory 
     * and adds it to the response.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     * 
     * @return void
     * @see \TechDivision\Servlet\Http\HttpServlet::doGet()
     */
    public function doGet(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {
        
        // explode the URI
        $uri = trim($servletRequest->getUri(), '/');
        list ($applicationName, $entity, $id) = explode('/', $uri, 3);
        
        // set the base URL for rendering images/thumbnails
        $this->getService($servletRequest)->setBaseUrl($this->getBaseUrl($servletRequest));
        $this->getService($servletRequest)->setWebappPath($servletRequest->getContext()->getWebappPath());
        
        // load file information and return the file object if possible
        $fileInfo = new \SplFileInfo($path = $this->getService($servletRequest)->thumbnail($id));
        if ($fileInfo->isDir()) {
            throw new FoundDirInsteadOfFileException(sprintf("Requested file %s is a directory", $path));
        }
        if ($fileInfo->isFile() === false) {
            throw new FileNotFoundException(sprintf('File %s not not found', $path));
        }
        if ($fileInfo->isReadable() === false) {
            throw new FileNotReadableException(sprintf('File %s is not readable', $path));
        }
        
        // open the file itself
        $file = $fileInfo->openFile();
        
        // set mimetypes to header
        $servletResponse->addHeader(
            'Content-Type',
            MimeTypes::getMimeTypeByExtension(
                pathinfo(
                    $file->getFilename(),
                    PATHINFO_EXTENSION
                )
            )
        );
        
        // set last modified date from file
        $servletResponse->addHeader('Last-Modified', gmdate('D, d M Y H:i:s \G\M\T', $file->getMTime()));
        
        // set expires date
        $servletResponse->addHeader('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
        
        // check if If-Modified-Since header info is set
        if ($servletRequest->getHeader('If-Modified-Since')) {
            // check if file is modified since header given header date
            if (strtotime($servletRequest->getHeader('If-Modified-Since')) >= $file->getMTime()) {
                // send 304 Not Modified Header information without content
                $servletResponse->addHeader('status', 'HTTP/1.1 304 Not Modified');
                $servletResponse->appendBodyStream(PHP_EOL);
                return;
            }
        }
        
        // add the thumbnail as response content
        $servletResponse->appendBodyStream(file_get_contents($file->getRealPath()));
    }
}
