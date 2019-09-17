<?php

declare(strict_types=1);

namespace Microsite;

abstract class Site
{
    const ERROR_PAGES_FOLDER_DOES_NOT_EXIST = 38001;
    
    const ERROR_NO_PAGES_FOUND = 38002;
    
    const ERROR_CANNOT_INSTANTIATE_PAGE = 38003;
    
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
    * @var Page[]
    */
    protected $pages;
    
   /**
    * @var \AppUtils\Request
    */
    protected $request;
    
   /**
    * @var UI
    */
    protected $ui;

   /**
    * @var UI_Navigation
    */
    protected $navigation;
    
    public function __construct(string $namespace, string $webrootFolder, string $webrootUrl)
    {
        $this->webrootFolder = $webrootFolder;
        $this->webrootUrl = $webrootUrl;
        $this->namespace = $namespace;
        $this->installFolder = realpath(__DIR__.'/../../');
        $this->request = new \AppUtils\Request();
        $this->ui = new UI($this);
        
        $this->initPages();
    }

    abstract public function getDefaultPageID() : string;
    
    abstract public function getDocumentTitle() : string;
    
    abstract protected function configureNavigation() : void;
    
   /**
    * This is similar to the document title: it is displayed
    * in the navigation as the title of the website.
    * 
    * @return string
    */
    abstract public function getNavigationTitle() : string;
    
    public function getRequest()
    {
        return $this->request;
    }
    
    public function getUI() : UI
    {
        return $this->ui;
    }
    
    public function getWebrootURL() : string
    {
        return $this->webrootUrl;
    }
    
    public function getWebrootFolder() : string
    {
        return $this->webrootFolder;
    }
   
    public function getInstallFolder() : string
    {
        return $this->installFolder;
    }
    
    public function getNamespace() : string
    {
        return $this->namespace;
    }
    
    public function render() : string
    {
        $tpl = $this->ui->createTemplate('Document');

        $page = $this->getActivePage();
        
        $tpl->setVar('page-content', $page->render());
        
        return $tpl->render();
    }
    
    public function getActivePageID() : string
    {
        return $this->getActivePage()->getID();
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
        
        $action = $this->request
        ->registerParam('action')
        ->setEnum($this->getURLNames())
        ->get();
        
        if(empty($action)) {
            $action = $this->getDefaultPage()->getURLName();
        }
        
        $this->activePage = $this->getPageByURLName($action);
        
        return $this->activePage;
    }
    
    public function display() : void
    {
        echo $this->render();
    }
    
    public function getDefaultPage() : Page
    {
        $id = $this->getDefaultPageID();
        
        $page = $this->getPageByID($id);
        
        if($page !== null) {
            return $page;
        }
        
        throw new \Exception(
            sprintf(
                'The default page [%s] does not exist.',
                $id
            ),
            self::ERROR_DEFAULT_PAGE_DOES_NOT_EXIST
        );
    }
    
    public function getPageByID(string $id) : ?Page
    {
        foreach($this->pages as $page) 
        {
            if($page->getID() === $id) {
                return $page;
            }
        }
        
        return null;
    }
    
    public function getPageByURLName($urlName) : ?Page
    {
        foreach($this->pages as $page)
        {
            if($page->getURLName() === $urlName) {
                return $page;
            }
        }
        
        return null;
    }
    
   /**
    * Retrieves an indexed array with the URL names for
    * all available pages.
    * 
    * @return string[]
    */
    protected function getURLNames() : array
    {
        $result = array();
        
        foreach($this->pages as $page) {
            $result[] = $page->getURLName();
        }
        
        return $result;
    }
    
    public function addWarning($message) : Site_Message
    {
        return $this->addMessage(Site_Message::MESSAGE_TYPE_WARNING, $message);
    }

    public function addError($message) : Site_Message
    {
        return $this->addMessage(Site_Message::MESSAGE_TYPE_ERROR, $message);
    }
    
    public function addInfo($message) : Site_Message
    {
        return $this->addMessage(Site_Message::MESSAGE_TYPE_INFO, $message);
    }

    public function addNotice($message) : Site_Message
    {
        return $this->addMessage(Site_Message::MESSAGE_TYPE_INFO, $message);
    }
    
    protected function initPages() : void
    {
        $pagesFolder = $this->webrootFolder.'/assets/classes/Pages';
        
        if(!file_exists($pagesFolder)) {
            throw new \Exception(
                'The [assets/classes/Pages] folder does not exist.' ,
                self::ERROR_PAGES_FOLDER_DOES_NOT_EXIST
            );
        }
        
        $names = \AppUtils\FileHelper::createFileFinder($pagesFolder)->getPHPClassNames();
        
        if(empty($names)) {
            throw new \Exception(
                'No pages found in the [assets/classes/Pages] folder.',
                self::ERROR_NO_PAGES_FOUND
            );
        }   
        
        foreach($names as $name) 
        {
            $className = $this->namespace.'\\Page_'.$name;
            
            if(!class_exists($className)) {
                throw new \Exception(
                    sprintf(
                        'Cannot initialize page [%s], the class [%s] was not found.',
                        $name,
                        $className
                    ),    
                    self::ERROR_CANNOT_INSTANTIATE_PAGE
                );
            }
            
            $this->pages[] = new $className($this);
        }
    }
    
    protected function addMessage($type, $message) : Site_Message
    {
        $message = new Site_Message($type, $message);
        
        $_SESSION['messages'][] = $message;
        
        return $message;
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