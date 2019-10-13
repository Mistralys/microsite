<?php

declare(strict_types=1);

namespace Microsite;

abstract class UI_Template implements Interface_Renderable
{
    use Traits_Renderable;
    
   /**
    * @var Site
    */
    protected $site;
    
   /**
    * @var UI
    */
    protected $ui;
    
    protected $vars = array();
    
   /**
    * @var Page
    */
    protected $activePage;
    
    public function __construct(UI $ui)
    {
        $this->site = $ui->getSite();
        $this->ui = $ui;
        $this->activePage = $this->site->getActivePage();
    }
    
    protected function init()
    {
        
    }
    
    public function createTemplate($id) : UI_Template
    {
        return $this->ui->createTemplate($id);
    }
    
    public function setVar(string $name, $value) : UI_Template
    {
        $this->vars[$name] = $value;
        return $this;
    }
    
    public function setVars(array $vars) : UI_Template
    {
        $this->vars = array_merge($this->vars, $vars);
        return $this;
    }
    
    public function getVar($name)
    {
        if(isset($this->vars[$name])) {
            return $this->vars[$name];
        }
        
        return null;
    }
    
    protected function preRender()
    {
        $this->init();
    }
}