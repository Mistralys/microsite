<?php

declare(strict_types=1);

namespace Microsite;

abstract class UI_Template implements Interface_Renderable, Interface_Loggable
{
    use Traits_Renderable;
    use Traits_Loggable;
    
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
    
   /**
    * @var string
    */
    protected $id;
    
    public function __construct(UI $ui, string $id)
    {
        $this->site = $ui->getSite();
        $this->id = $id;
        $this->ui = $ui;
        $this->activePage = $this->site->getActivePage();
    }
    
    public function getID() : string
    {
        return $this->id;
    }

   /**
    * Initializes the template: this is where variables
    * are fetched and verfied as needed, before the template
    * is rendered.
    */
    abstract protected function init();
    
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
    
    public function getLogPrefix() : string
    {
        return 'Template ['.$this->getID().']';
    }
    
    protected function preRender()
    {
        $this->log('Initializing the template for rendering.');
        
        $this->init();
        
        $this->log('Rendering the template.');
    }
}