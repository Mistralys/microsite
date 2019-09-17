<?php

declare(strict_types=1);

namespace Microsite;

abstract class Site
{
    const ERROR_PAGES_FOLDER_DOES_NOT_EXIST = 38001;
    
    const ERROR_NO_PAGES_FOUND = 38002;
    
    const ERROR_CANNOT_INSTANTIATE_PAGE = 38003;
    
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
    
    public function __construct(string $namespace, string $webrootFolder, string $webrootUrl)
    {
        $this->webrootFolder = $webrootFolder;
        $this->webrootUrl = $webrootUrl;
        $this->namespace = $namespace;
        $this->installFolder = realpath(__DIR__.'/../../');
        $this->request = new \AppUtils\Request();
        
        $this->initPages();
    }
    
    public function start()
    {
        
    }
    
    public function addWarning($message)
    {
        $this->addMessage('warning', $message);
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
            $className = $this->namespace.'\\'.$name;
            
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