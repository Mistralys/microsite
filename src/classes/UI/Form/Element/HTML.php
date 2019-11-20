<?php

namespace Microsite;

class UI_Form_Element_HTML extends UI_Form_Element
{
    protected $html = '';
    
    protected function _renderElement() : string
    {
        return $this->html;
    }
    
    public function setHTML($html)
    {
        $this->html = $html;
        return $this;
    }

    public function supportsValue()
    {
        return false;
    }
    
    public function supportsWrapper()
    {
        return false;
    }
    
}