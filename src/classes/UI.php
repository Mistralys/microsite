<?php

declare(strict_types=1);

namespace Microsite;

class UI
{
    protected $jsHead = array();
    
    protected $jsOnload = array();
    
    protected $scripts = array();
    
   /**
    * @var Site
    */
    protected $site;
    
    public function __construct(Site $site)
    {
        $this->site = $site;
    }
    
    public function addJSHead(string $statement) : UI
    {
        $this->jsHead[] = rtrim($statement, ';');
        return $this;
    }
    
    public function addJSOnload(string $statement) : UI
    {
        $this->jsOnload[] = rtrim($statement, ';');
        return $this;
    }
    
    public function addScript(string $filename) : UI
    {
        if(!in_array($filename, $this->scripts)) {
            $this->scripts[] = $filename;
        }
        
        return $this;
    }
    
    public function addVendorScript(string $packageName, string $path) : UI
    {
        $url = $this->site->getWebrootURL().'/vendor/'.$packageName.'/'.$path;
        return $this->addScript($url);
    }
    
    public function addSiteScript(string $path) : UI
    {
        return $this->addVendorScript('mistralys/microsite', $path);
    }
    
    public function renderHead() : string
    {
        $lines = array();
        
        foreach($this->scripts as $script) 
        {
            $ext = \AppUtils\FileHelper::getExtension($script);
            
            switch($ext) {
                case 'js':
                    $lines[] = '<script src="'.$script.'"></script>';
                    break;
                    
                case 'css':
                    $lines[] = '<link href="'.$script.'" rel="stylesheet">';
                    break;
            }
            
        }
        
        $lines[] = '<script>';
        $lines[] = implode(';'.PHP_EOL, $this->jsHead).';';
        $lines[] = '$(document).ready(function() {';
        $lines[] = implode(';'.PHP_EOL, $this->jsOnload);
        $lines[] = '});';
        $lines[] = '</script>';
            
        return implode(PHP_EOL, $lines);
    }
    
    public function createTemplate(string $id) : UI_Template
    {
        $class = '\Microsite\UI_Template_'.$id;
        
        $tpl = new $class($this);
        
        return $tpl;
    }
    
    public function getSite() : Site
    {
        return $this->site;
    }
    
    public function createNavigation() : UI_Navigation
    {
        return new UI_Navigation($this, $this->site);
    }
}