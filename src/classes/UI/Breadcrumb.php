<?php

declare(strict_types=1);

namespace Microsite;

class UI_Breadcrumb
{
   /**
    * @var Page
    */
    protected $page;
    
    protected $items = array();
    
    public function __construct(Page $page)
    {
        $this->page = $page;
    }
    
    public function add(string $label, string $url) : UI_Breadcrumb
    {
        $this->items[] = array(
            'label' => $label,
            'url' => $url,
        );
        
        return $this;
    }
    
    public function render()
    {
        if(empty($this->items)) {
            return '';
        }
        
        $total = count($this->items);
        
        $html = 
        '<ol class="breadcrumb">';
            for($i=0; $i < $total; $i++) 
            {
                $item = $this->items[$i];
                
                if($i == $total-1) {
                    $html .= sprintf(
                        '<li class="active">%s</li>',
                        $item['label']
                    );
                    break;
                }
                
                $html .= sprintf(
                    '<li><a href="%s">%s</a></li>',
                    $item['url'],
                    $item['label']
                );
            }
            $html .=
        '</ol>';
            
        return $html;
    }
    
    public function display()
    {
        echo $this->render();
    }
    
    public function addHomepage() : UI_Breadcrumb
    {
        $home = $this->page->getSite()->getDefaultPage();
        $this->add($home->getNavigationTitle(), $home->buildURL());
        
        return $this;
    }
}