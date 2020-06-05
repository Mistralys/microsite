<?php

declare(strict_types=1);

namespace Microsite;

use AppUtils\RegexHelper;

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
    
    public function setType(string $type)
    {
        return $this->setAttribute('type', $type);
    }
    
    protected function _renderElement() : string
    {
        $this->addClass('form-control');
        $this->setAttribute('value', (string)$this->getValue());
        
        return '<input'.$this->renderAttributes().'/>';
    }
    
    public function setPlaceholder($placeholder)
    {
        return $this->setAttribute('placeholder', $placeholder);
    }
    
    public function validateLabel() : UI_Form_Element_String
    {
        return $this->validateRegex(RegexHelper::REGEX_LABEL, 'Invalid label');
    }
    
    public function validateTitle() : UI_Form_Element_String
    {
        return $this->validateRegex(RegexHelper::REGEX_NAME_OR_TITLE, 'Invalid name/title');
    }
    
    public function validateAlias() : UI_Form_Element_String
    {
        return $this->validateRegex(RegexHelper::REGEX_ALIAS, 'Invalid alias');
    }
    
    public function validateEmail() : UI_Form_Element_String
    {
        return $this->validateRegex(RegexHelper::REGEX_EMAIL, 'Invalid email');
    }
    
    public function validateURL() : UI_Form_Element_String
    {
        return $this->validateRegex(RegexHelper::REGEX_URL, 'Invalid URL');
    }
}
