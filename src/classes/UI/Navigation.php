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
    
   /**
    * @var UI_Navigation_Item[]
    */
    protected $items = array();
    
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
    
    public function hasItems() : bool
    {
        return !empty($this->items);
    }
    
   /**
    * @return UI_Navigation_Item[]
    */
    public function getItems()
    {
        return $this->items;
    }
    
    public function getLimitParams() : array
    {
        return $this->limitParams;
    }
    
   /**
    * Adds a parameter to limit the URL detection to,
    * to make sure only the relevant ones are used
    * to detect the active navigation item.
    * 
    * @param string $name
    * @return UI_Navigation
    */
    public function addLimitParameter(string $name) : UI_Navigation
    {
        if(!in_array($name, $this->limitParams)) {
            $this->limitParams[] = $name;
        }
        
        return $this;
    }
    
    public function addLimitParameters(array $names) : UI_Navigation
    {
        foreach($names as $name) {
            $this->addLimitParameter($name);
        }
        
        return $this;
    }
    
    public function addURL(string $label, string $url) : UI_Navigation_Item
    {
        $item = new UI_Navigation_Item(
            $this, 
            $label,
            $url
        );
        
        $this->items[] = $item;
        
        return $item;
    }
    
    public function addPage(Page $page) : UI_Navigation_Item 
    {
         return $this->addURL($page->getNavigationTitle(), $page->buildURL());
    }
}