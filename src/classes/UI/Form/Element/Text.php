<?php

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
    
    protected function _render()
    {
        $this->addClass('form-control');
        
        return '<textarea'.$this->renderAttributes().'>'.$this->getValue().'</textarea>';
    }
    
    public function setPlaceholder($placeholder)
    {
        return $this->setAttribute('placeholder', $placeholder);
    }
    
    public function setRows($rows)
    {
        return $this->setAttribute('rows', $rows);
    }

    public function setColumns($cols)
    {
        return $this->setAttribute('cols', $cols);
    }
}