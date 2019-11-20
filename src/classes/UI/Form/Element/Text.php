<?php

declare(strict_types=1);

namespace Microsite;

class UI_Form_Element_Text extends UI_Form_Element
{
    public function supportsValue()
    {
        return true;
    }
    
    public function supportsWrapper()
    {
        return true;
    }
    
    protected function _renderElement() : string
    {
        $this->addClass('form-control');
        
        return '<textarea'.$this->renderAttributes().'>'.$this->getValue().'</textarea>';
    }
    
    public function setPlaceholder(string $placeholder) : UI_Form_Element_Text
    {
        return $this->setAttribute('placeholder', $placeholder);
    }
    
    public function setRows(int $rows) : UI_Form_Element_Text
    {
        return $this->setAttribute('rows', (string)$rows);
    }

    public function setColumns(int $cols) : UI_Form_Element_Text
    {
        return $this->setAttribute('cols', (string)$cols);
    }
    
    public function getValue()
    {
        $value = parent::getValue();
        
        if($value===null) {
            return '';
        }
        
        return (string)$value;
    }
}
