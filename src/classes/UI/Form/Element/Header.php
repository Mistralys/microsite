<?php

namespace Microsite;

class UI_Form_Element_Header extends UI_Form_Element
{
    protected function _render()
    {
        return '<h3>'.$this->label.'</h3>';
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