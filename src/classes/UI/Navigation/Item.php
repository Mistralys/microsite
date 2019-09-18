<?php

declare(strict_types=1);

namespace Microsite;

class UI_Navigation_Item
{
    protected $nav;
    
    protected $label;
    
    protected $url;
    
   /**
    * @var \AppUtils\Request
    */
    protected $request;
    
   /**
    * @var bool
    */
    protected $active;
    
    public function __construct(UI_Navigation $nav, $label, $url)
    {
        $this->nav = $nav;
        $this->label = $label;
        $this->url = $url;
        $this->request = $nav->getRequest();
    }
    
    public function setLabel(string $label) : UI_Navigation_Item
    {
        $this->label = $label;
        return $this;
    }
    
    public function setURL(string $url)
    {
        $this->url = $url;
        return $this;
    }
    
    public function isActive() : bool
    {
        if(!isset($this->active)) 
        {
            $this->active = $this->request->createURLComparer(
                $this->request->getCurrentURL(),
                $this->url,
                $this->nav->getLimitParams()
            )
            ->isMatch();
        }
        
        return $this->active; 
    }
    
    public function getURL()
    {
        return $this->url;
    }
    
    public function getLabel()
    {
        return $this->label;
    }
}