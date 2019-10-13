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
    
   /**
    * Creates a new instance of a template.
    * @param string $id
    * @return UI_Template
    */
    public function createTemplate(string $id) : UI_Template
    {
        $class = '\Microsite\UI_Template_'.$id;
        
        $tpl = new $class($this);
        
        return $tpl;
    }
    
   /**
    * Creates a new data grid instance, which can be used to 
    * render an HTML table.
    * 
    * @return UI_DataGrid
    */
    public function createDataGrid() : UI_DataGrid
    {
        $grid = new UI_DataGrid($this);
        return $grid;
    }
    
   /**
    * Retrieves the site instance.
    * @return Site
    */
    public function getSite() : Site
    {
        return $this->site;
    }
    
   /**
    * Creates a new navigation instance.
    * @return UI_Navigation
    */
    public function createNavigation() : UI_Navigation
    {
        return new UI_Navigation($this, $this->site);
    }
}