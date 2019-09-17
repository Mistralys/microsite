<?php

namespace Microsite;

class UI_Breadcrumb
{
   /**
    * @var Page
    */
    protected $page;
    
    protected $items;
    
    public function __construct(Page $page)
    {
        $this->page = $page;
    }
    
    public function add($label, $url)
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
    
    public function addOverview()
    {
        $this->add('Overview', '?action=overview');
    }
}