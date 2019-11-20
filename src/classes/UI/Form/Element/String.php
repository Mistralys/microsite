<?php

namespace Microsite;

class UI_Form_Element_String extends UI_Form_Element
{
    public function supportsValue()
    {
        return true;
    }
    
    public function supportsWrapper()
    {
        return true;
    }
    
    protected function init()
    {
        $this->setType('text');
    }
    
    public function setType($type)
    {
        return $this->setAttribute('type', $type);
    }
    
    protected function _renderElement() : string
    {
        $this->addClass('form-control');
        $this->setAttribute('value', $this->getValue());
        
        return '<input'.$this->renderAttributes().'/>';
    }
    
    public function setPlaceholder($placeholder)
    {
        return $this->setAttribute('placeholder', $placeholder);
    }
    
    public function validateLabel()
    {
        return $this->validateRegex('/\A[^<>"]+/s', 'Invalid label');
    }
    
    public function validateAlias()
    {
        return $this->validateRegex('/\A[a-z][0-9_a-z-.]{1,80}\Z/', 'Invalid alias');
    }
}