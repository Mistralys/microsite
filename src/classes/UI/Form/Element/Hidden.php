<?php

namespace Microsite;

class UI_Form_Element_Hidden extends UI_Form_Element
{
    protected function _renderElement() : string
    {
        $this->setAttribute('type', 'hidden');
        $this->setAttribute('value', $this->getValue());
        
        return '<input'.$this->renderAttributes().'/>';
    }
    
    public function supportsValue()
    {
        return true;
    }
    
    public function supportsWrapper()
    {
        return false;
    }
}