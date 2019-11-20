<?php

namespace Microsite;

class UI_Form_Element_Select extends UI_Form_Element
{
    public function supportsValue()
    {
        return true;
    }
    
    public function supportsWrapper()
    {
        return true;
    }
    
    protected $options;
    
    public function addOption($id, $label=null)
    {
        $option = new UI_Form_Element_Select_Option($id, $label);
        $this->options[] = $option;
        return $option;
    }
    
    protected function _renderElement() : string
    {
        $this->addClass('form-control');
        
        $html = 
        '<select'.$this->renderAttributes().'>';
            foreach ($this->options as $option) {
                $html .= $option->render();
            }
            $html .=
        '</select>';
            
        return $html;
    }
}

class UI_Form_Element_Select_Option
{
    protected $id;
    
    protected $label;
    
    public function __construct($id, $label=null)
    {
        $this->id = $id;
        $this->label = $label;
    }
    
    public function render()
    {
        $label = $this->id;
        if(!empty($this->label)) {
            $label = $this->label;
        }
        
        return sprintf(
            '<option value="%s">%s</option>',
            $this->id,
            $label
        );
    }
}