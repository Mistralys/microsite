<?php

declare(strict_types=1);

namespace Microsite;

abstract class Site extends Page
{
    const ERROR_PAGES_FOLDER_DOES_NOT_EXIST = 38001;
    
    const ERROR_NO_PAGES_FOUND = 38002;
    
    const ERROR_DEFAULT_PAGE_DOES_NOT_EXIST = 38004;
    
    const ERROR_SESSIONS_DISABLED = 38005;
    
   /**
    * @var string
    */
    protected $webrootFolder;
    
   /**
    * @var string
    */
    protected $webrootUrl;
   
   /**
    * @var string
    */
    protected $installFolder;
    
   /**
    * @var string
    */
    protected $namespace;
    
   /**
    * @var \AppUtils\Request
    */
    protected $request;
    
   /**
    * @var UI
    */
    protected $ui;

    public function __construct(string $namespace, string $webrootFolder, string $webrootUrl)
    {
        $this->webrootFolder = $webrootFolder;
        $this->webrootUrl = $webrootUrl;
        $this->namespace = $namespace;
        $this->installFolder = realpath(__DIR__.'/../../');

        $this->startSession();
        
        $this->request = new \AppUtils\Request();
        $this->ui = new UI($this);
        
        $this->initLocales();
        
        parent::__construct($this);
        
        if(empty($this->subpages)) {
            throw new Exception(
                'No pages found in the [assets/classes/Page] folder.',
                self::ERROR_NO_PAGES_FOUND
            );
        }
    }
    
    protected function startSession()
    {
        switch(session_status())
        {
            case PHP_SESSION_DISABLED:
                throw new Exception(
                    'Cannot start site: sessions are disabled.',
                    null,
                    self::ERROR_SESSIONS_DISABLED
                );
                
            case PHP_SESSION_NONE:
                session_start();
                break;
        }
    }
    
    protected function initLocales()
    {
        \AppLocalize\Localization::addAppLocale('de_DE');
        \AppLocalize\Localization::addAppLocale('fr_FR');
        
        \AppLocalize\Localization::addSourceFolder(
            'microsite-server',
            'PHP class strings',
            'Microsite',
            $this->installFolder.'/localization',
            $this->installFolder.'/src'
        );

        \AppLocalize\Localization::addSourceFolder(
            'microsite-client',
            'JavaScript strings',
            'Microsite',
            $this->installFolder.'/localization',
            $this->installFolder.'/js'
        );
        
        \AppLocalize\Localization::addSourceFolder(
            $this->getNamespace().'-classes',
            'PHP class strings',
            $this->getNamespace(),
            $this->webrootFolder.'/localization',
            $this->webrootFolder.'/assets'
        );
        
        \AppLocalize\Localization::addSourceFolder(
            'microsite',
            'JavaScript strings',
            $this->getNamespace(),
            $this->webrootFolder.'/localization',
            $this->webrootFolder.'/js'
        );
        
        \AppLocalize\Localization::configure(
            $this->webrootFolder.'/localization/cache.json',
            $this->webrootFolder.'/js'
        );
        
        // FIXME implement this selection
        // \AppLocalize\Localization::selectAppLocale('de_DE');
    }

    abstract public function getDocumentTitle() : string;
    
    public function getPageTitle() : string
    {
        return '';
    }
    
    public function getPageAbstract() : string
    {
        return '';
    }
    
    public function getWebrootURL() : string
    {
        return $this->webrootUrl;
    }
    
    public function getWebrootFolder() : string
    {
        return $this->webrootFolder;
    }
    
    public function getClassesFolder() : string
    {
        return $this->webrootFolder.'/assets/classes';
    }
   
    public function getInstallFolder() : string
    {
        return $this->installFolder;
    }
    
    public function getNamespace() : string
    {
        return $this->namespace;
    }
    
    protected function _render() : string
    {
        $tpl = $this->ui->createTemplate('Document');

        $tpl->setVar('page-content', $this->getActivePage()->render());
        
        return $tpl->render();
    }
    
    protected function _renderContent() : string
    {
        // unused for the site.
    }
    
    protected function initRender()
    {
        $this->getActivePage()->handleAJAX();
    }
    
    public function getActiveSlug() : string
    {
        return $this->getActivePage()->getSlug();
    }
    
   /**
    * @var Page
    */
    protected $activePage;
    
    public function getActivePage() : Page
    {
        if(isset($this->activePage)) {
            return $this->activePage;
        }
        
        $slug = $this->request
        ->registerParam('slug')
        ->setEnum($this->getSlugs())
        ->get();
        
        if(empty($slug)) {
            $slug = $this->getDefaultPage()->getSlug();
        }
        
        $this->activePage = $this->getPageBySlug($slug);
        
        return $this->activePage;
    }
    
    public function getDefaultPage() : Page
    {
        $slug = $this->getDefaultSlug();
        
        $page = $this->getPageBySlug($slug);
        
        if($page !== null) {
            return $page;
        }
        
        throw new Exception(
            sprintf(
                'The default page [%s] does not exist.',
                $slug
            ),
            self::ERROR_DEFAULT_PAGE_DOES_NOT_EXIST
        );
    }
    
    protected function getOwnFolder() : string
    {
        $folder = $this->getClassesFolder().'/Page';
        
        if(!file_exists($folder)) {
            throw new Exception(
                'The [assets/classes/Page] folder does not exist.' ,
                self::ERROR_PAGES_FOLDER_DOES_NOT_EXIST
            );
        }
        
        return $folder;
    }
    
    /**
     * @return Site_Message[]
     */
    public function getMessages()
    {
        return $_SESSION['spex_messages'];
    }
    
    public function hasMessages() : bool
    {
        return !empty($_SESSION['spex_messages']);
    }
    
    public function clearMessages() : void
    {
        $_SESSION['spex_messages'] = array();
    }
    
    public static function boot(string $namespace, string $webrootFolder, string $webrootUrl) : Site
    {
        $localConfig = $webrootFolder.'/config-local.php';
        if(!file_exists($localConfig)) {
            die('<b>ERROR:</b> The local configuration file does not exist. See <a href="README.md">README</a> on how to create it.');
        }
        
        require_once $localConfig;
        
        $class = get_called_class();
        
        try
        {
            $obj = new $class($namespace, $webrootFolder, $webrootUrl);
        }
        catch(Exception $e)
        {
            $e->display();
        }
        
        return $obj;
    }

   /**
    * Retrieves the URL to view a media file on disk.
    * 
    * Generates a safe URL without file path information.
    * The file can be located anywhere on disk, but must
    * be readable.
    * 
    * @param string $mediaPath Path to the media file to display
    * @return string
    */
    public function getMediaURL(string $mediaPath) : string
    {
        $key = md5($mediaPath);
        
        if(!isset($_SESSION['displaymedia'])) {
            $_SESSION['displaymedia'] = array();
        }
        
        $_SESSION['displaymedia'][$key] = array(
            'path' => $mediaPath
        );
        
        $display = $this->getPageBySlug('DisplayMedia');
        
        return $display->buildURL(array('media' => $key));
    }
}