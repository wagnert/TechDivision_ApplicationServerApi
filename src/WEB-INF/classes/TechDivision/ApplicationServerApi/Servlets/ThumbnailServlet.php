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

use TechDivision\ApplicationServerApi\Service\AppService;
use TechDivision\ApplicationServerApi\Servlets\AbstractServlet;
use TechDivision\ServletContainer\Http\ServletRequest;
use TechDivision\ServletContainer\Http\ServletResponse;
use TechDivision\ServletContainer\Interfaces\ServletConfig;
use TechDivision\ServletContainer\Utilities\MimeTypeDictionary;
use TechDivision\ServletContainer\Exceptions\FileNotFoundException;
use TechDivision\ServletContainer\Exceptions\FileNotReadableException;
use TechDivision\ServletContainer\Exceptions\FoundDirInsteadOfFileException;

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
     * Assmbler to assemble app nodes to stdClass representation.
     *
     * @var \TechDivision\ApplicationServerApi\Service\AppService
     */
    protected $service;

    /**
     * Hold dictionary for mime types.
     *
     * @var \TechDivision\ServletContainer\Utilities\MimeTypeDictionary
     */
    protected $mimeTypeDictionary;

    /**
     * Initializes the servlet when the application server starts.
     *
     * @param \TechDivision\ServletContainer\Interfaces\ServletConfig $config The servlet configuration
     *
     * @return void
     * @see \TechDivision\ServletContainer\Servlets\GenericServlet::init()
     */
    public function init(ServletConfig $config)
    {
        
        // call parent init method
        parent::init($config);
        
        // initialize the mime type dictonary
        $this->setMimeTypeDictionary(new MimeTypeDictionary());
        
        // create a new service instance
        $initialContext = $this->getInitialContext();
        $this->setService(
            $initialContext->newInstance(
                ThumbnailServlet::SERVICE_CLASS,
                array(
                    $initialContext
                )
            )
        );
    }

    /**
     * Sets the mime type dictionary to use.
     *
     * @param \TechDivision\ServletContainer\Utilities\MimeTypeDictionary $mimeTypeDictionary The mime type dictionary to use
     * 
     * @return void
     */
    public function setMimeTypeDictionary(MimeTypeDictionary $mimeTypeDictionary)
    {
        $this->mimeTypeDictionary = $mimeTypeDictionary;
    }

    /**
     * Returns the mime type dictionary to use.
     *
     * @return \TechDivision\ServletContainer\Utilities\MimeTypeDictionary The mime type dictionary
     */
    public function getMimeTypeDictionary()
    {
        return $this->mimeTypeDictionary;
    }

    /**
     * Tries to load the requested thumbnail from the applications WEB-INF directory 
     * and adds it to the response.
     *
     * @param \TechDivision\ServletContainer\Http\ServletRequest  $servletRequest  The request instance
     * @param \TechDivision\ServletContainer\Http\ServletResponse $servletResponse The response instance
     * 
     * @return void
     * @see \TechDivision\ServletContainer\Interfaces\Servlet::doGet()
     */
    public function doGet(ServletRequest $servletRequest, ServletResponse $servletResponse)
    {
        
        // explode the URI
        $uri = trim($servletRequest->getUri(), '/');
        list ($applicationName, $entity, $id) = explode('/', $uri, 3);
        
        // set the base URL for rendering images/thumbnails
        $this->getService()->setBaseUrl($this->getBaseUrl($servletRequest));
        $this->getService()->setConfigurationPath($this->getConfigurationPath());
        
        // load file information and return the file object if possible
        $fileInfo = new \SplFileInfo($path = $this->getService()->thumbnail($id));
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
            $this->getMimeTypeDictionary()->find(
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
                $servletResponse->getContent(PHP_EOL);
                return;
            }
        }
        
        // add the thumbnail as response content
        $servletResponse->setContent(file_get_contents($file->getRealPath()));
    }
}
