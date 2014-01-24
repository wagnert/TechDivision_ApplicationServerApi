<?php

/**
 * TechDivision\ApplicationServerApi\Servlets\ThumbnailServlet
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServerApi\Servlets;

use TechDivision\ServletContainer\Interfaces\Request;
use TechDivision\ServletContainer\Interfaces\Response;
use TechDivision\ServletContainer\Interfaces\ServletConfig;
use TechDivision\ServletContainer\Utilities\MimeTypeDictionary;
use TechDivision\ServletContainer\Exceptions\FileNotFoundException;
use TechDivision\ServletContainer\Exceptions\FileNotReadableException;
use TechDivision\ServletContainer\Exceptions\FoundDirInsteadOfFileException;
use TechDivision\ApplicationServerApi\Service\AppService;
use TechDivision\ApplicationServerApi\Servlets\AbstractServlet;

/**
 *
 * @package TechDivision\ApplicationServerApi
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim <tw@techdivision.com>
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
     * (non-PHPdoc)
     *
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
        $this->setService($initialContext->newInstance(ThumbnailServlet::SERVICE_CLASS, array(
            $initialContext
        )));
    }

    /**
     * Set's the mime type dictionary to use.
     *
     * @param \TechDivision\ServletContainer\Utilities\MimeTypeDictionary $mimeTypeDictionary
     *            The mime type dictionary to use
     * @return void
     */
    public function setMimeTypeDictionary(MimeTypeDictionary $mimeTypeDictionary)
    {
        $this->mimeTypeDictionary = $mimeTypeDictionary;
    }

    /**
     * Return's the mime type dictionary to use.
     *
     * @return \TechDivision\ServletContainer\Utilities\MimeTypeDictionary The mime type dictionary
     */
    public function getMimeTypeDictionary()
    {
        return $this->mimeTypeDictionary;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ServletContainer\Servlets\HttpServlet::doGet()
     */
    public function doGet(Request $req, Response $res)
    {
        
        // explode the URI
        $uri = trim($req->getUri(), '/');
        list ($applicationName, $entity, $id) = explode('/', $uri, 3);
        
        // set the base URL for rendering images/thumbnails
        $this->getService()->setBaseUrl($this->getBaseUrl($req));
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
        $res->addHeader('Content-Type', $this->getMimeTypeDictionary()
            ->find(pathinfo($file->getFilename(), PATHINFO_EXTENSION)));
        
        // set last modified date from file
        $res->addHeader('Last-Modified', gmdate('D, d M Y H:i:s \G\M\T', $file->getMTime()));
        
        // set expires date
        $res->addHeader('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
        
        // check if If-Modified-Since header info is set
        if ($req->getHeader('If-Modified-Since')) {
            // check if file is modified since header given header date
            if (strtotime($req->getHeader('If-Modified-Since')) >= $file->getMTime()) {
                // send 304 Not Modified Header information without content
                $res->addHeader('status', 'HTTP/1.1 304 Not Modified');
                $res->getContent(PHP_EOL);
                return;
            }
        }
        
        // add the thumbnail as response content
        $res->setContent(file_get_contents($file->getRealPath()));
    }
}