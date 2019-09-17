<?php

declare(strict_types = 1);

namespace Microsite;

class UI_Navigation
{
    /**
     *
     * @var UI
     */
    protected $ui;

   /**
    * @var \AppUtils\Request
    */
    protected $request;
    
   /**
    * @var string[]
    */
    protected $limitParams = array();
    
    public function __construct(UI $ui, Site $site)
    {
        $this->ui = $ui;
        $this->request = $site->getRequest();
    }
    
    public function getRequest() : \AppUtils\Request
    {
        return $this->request;
    }
    
    public function getUI() : UI
    {
        return $this->ui;
    }
    
    public function getLimitParams() : array
    {
        return $this->limitParams;
    }
    
    public function addLimitParameter(string $name) : UI_Navigation
    {
        if(!in_array($name, $this->limitParams)) {
            $this->limitParams[] = $name;
        }
        
        return $this;
    }
    
    public function addItem(string $label, string $url) : UI_Navigation_Item
    {
        $item = new UI_Navigation_Item(
            $this, 
            $label,
            $url
        );
        
        $this->items[] = $item;
        
        return $item;
    }
}