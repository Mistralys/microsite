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
    
    public function isActive() : bool
    {
        if(!isset($this->isActive())) 
        {
            $this->active = $this->request->urlsMatch(
                $this->request->getCurrentURL(),
                $this->url,
                $this->nav->getLimitParams()
            );
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