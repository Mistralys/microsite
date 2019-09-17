<?php

namespace Microsite;

abstract class Site
{
    protected $webrootFolder;
    
    protected $webrootUrl;
    
    protected $installFolder;
    
    public function __construct($webrootFolder, $webrootUrl)
    {
        $this->webrootFolder = $webrootFolder;
        $this->webrootUrl = $webrootUrl;
        $this->installFolder = realpath(__DIR__.'/../../');
    }
    
    public function start()
    {
        
    }
    
    public function addWarning($message)
    {
        $this->addMessage('warning', $message);
    }
    
    protected function addMessage($type, $message)
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
    
    public function hasMessages()
    {
        return !empty($_SESSION['messages']);
    }
    
    public function clearMessages()
    {
        $_SESSION['messages'] = array();
    }
    
    public static function boot($webrootFolder, $webrootUrl)
    {
        $localConfig = $webrootFolder.'/config-local.php';
        if(!file_exists($localConfig)) {
            die('<b>ERROR:</b> The local configuration file does not exist. See <a href="README.md">README</a> on how to create it.');
        }
        
        require_once $localConfig;
        
        $class = get_called_class();
        
        $site = new $class($webrootFolder, $webrootUrl);
        $site->start();
    }
}