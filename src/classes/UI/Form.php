<?php

namespace Microsite;

class UI_Form
{
   /**
    * @var Page
    */
    protected $page;
    
    protected $id;
    
    public function __construct(Page $page)
    {
        $this->page = $page;
        $this->id = md5('form_'.$this->page->getID());
        $this->request = $page->getRequest();
        
        $this->setHidden($this->id.'_save', 'yes');
    }
    
    public function setHidden($name, $value)
    {
        if(!isset($this->elements[$name])) {
            $this->elements[$name] = new UI_Form_Element_Hidden($this, $name);
        } else if(!$this->elements[$name] instanceof UI_Form_Element_Hidden) {
            throw new \Exception('Cannot set hidden ['.$name.'], another element type already uses this name.');
        }
        
        $this->elements[$name]->setValue($value);
        
        return $this;
    }
    
    public function addButton($html)
    {
        $this->buttons[] = $html;
        return $this;
    }
    
    public function hasContent()
    {
        return !empty($this->elements);
    }
    
    public function render()
    {
        $html =
        '<form method="POST">';
        
        foreach($this->elements as $element) {
            $html .= $element->render();
        }
        
        if(!empty($this->buttons)) 
        {
            $html .=
            '<hr>'.
            '<p>';
                foreach($this->buttons as $button) {
                    $html .= $button.' ';
                }
                $html .=
            '</p>';
        }
        
        $html .=
        '</form>';
        
        return $html;
    }
    
    public function isSubmitted()
    {
        return $this->request->getBool($this->id.'_save');
    }
    
    public function validate()
    {
        if(!$this->isSubmitted()) {
            return false;
        }
        
        $valid = true;
        foreach($this->elements as $element) {
            if(!$element->validate()) {
                $valid = false;
            }
        }
        
        return $valid;
    }
    
    protected $htmlCounter = 0;
    
    public function addHTML($html)
    {
        $this->htmlCounter++;
        
        $name = 'form_html_'.$this->htmlCounter; 
        
        require_once 'Form/Element/HTML.php';
        
        $el = new UI_Form_Element_HTML($this, $name);
        $el->setHTML($html);
        
        $this->elements[$name] = $el;
        
        return $this;
    }
    
   /**
    * @var UI_Form_Element[]
    */
    protected $elements;
    
    public function addSelect($name, $label)
    {
        require_once 'Form/Element/Select.php';
        
        $el = new UI_Form_Element_Select($this, $name);
        $el->setLabel($label);
        
        $this->elements[$name] = $el;
        
        return $el;
    }
    
    protected $headerCount = 0;
    
    public function addHeader($label)
    {
        $this->headerCount++;
        
        $name = 'form_header_'.$this->headerCount;
        
        require_once 'Form/Element/Header.php';
        
        $el = new UI_Form_Element_Header($this, $name);
        $el->setLabel($label);
        
        $this->elements[$name] = $el;
        
        return $el;
    }

    public function addString($name, $label)
    {
        require_once 'Form/Element/String.php';
        
        $el = new UI_Form_Element_String($this, $name);
        $el->setLabel($label);
        
        $this->elements[$name] = $el;
        
        return $el;
    }

   /**
    * @param string $name
    * @param string $label
    * @return UI_Form_Element_Text
    */
    public function addText($name, $label)
    {
        require_once 'Form/Element/Text.php';
        
        $el = new UI_Form_Element_Text($this, $name);
        $el->setLabel($label);

        $this->elements[$name] = $el;
        
        return $el;
    }
    
    public function getValues()
    {
        $values = array();
        
        foreach($this->elements as $element) {
            if($element->supportsValue()) {
                $values[$element->getName()] = $element->getValue();
            }
        }
        
        return $values;
    }
    
    public function display()
    {
        echo $this->render();
    }
}