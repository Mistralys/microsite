<?php

declare(strict_types=1);

namespace Microsite;

abstract class Site extends Page
{
    const ERROR_PAGES_FOLDER_DOES_NOT_EXIST = 38001;
    
    const ERROR_NO_PAGES_FOUND = 38002;
    
    const ERROR_DEFAULT_PAGE_DOES_NOT_EXIST = 38004;
    
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
        
        $this->request = new \AppUtils\Request();
        $this->ui = new UI($this);
     
        parent::__construct($this);
        
        if(empty($this->pages)) {
            throw new \Exception(
                'No pages found in the [assets/classes/Page] folder.',
                self::ERROR_NO_PAGES_FOUND
            );
        }
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
        
        throw new \Exception(
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
            throw new \Exception(
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
        return $_SESSION['messages'];
    }
    
    public function hasMessages() : bool
    {
        return !empty($_SESSION['messages']);
    }
    
    public function clearMessages() : void
    {
        $_SESSION['messages'] = array();
    }
    
    public static function boot(string $namespace, string $webrootFolder, string $webrootUrl) : Site
    {
        $localConfig = $webrootFolder.'/config-local.php';
        if(!file_exists($localConfig)) {
            die('<b>ERROR:</b> The local configuration file does not exist. See <a href="README.md">README</a> on how to create it.');
        }
        
        require_once $localConfig;
        
        $class = get_called_class();
        
        return new $class($namespace, $webrootFolder, $webrootUrl);
    }
}