<?php

declare(strict_types=1);

namespace Microsite;

class UI_Form implements Interface_Renderable
{
    use Traits_Renderable;
    
    const ERROR_HIDDEN_OTHER_ELEMENT_TYPE = 41601;
    
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
        
        $this->setHidden($this->getSubmitVarName(), 'yes');
    }
    
    public function setHidden(string $name, string $value) : UI_Form
    {
        if(!isset($this->elements[$name])) 
        {
            $this->elements[$name] = new UI_Form_Element_Hidden($this, $name);
        } 
        else if(!$this->elements[$name] instanceof UI_Form_Element_Hidden) 
        {
            throw new Exception(
                sprintf(
                    'Cannot set hidden [%s], an element of type [%s] already uses this name.',
                    $name,
                    get_class($this->elements[$name])
                ),
                null,
                self::ERROR_HIDDEN_OTHER_ELEMENT_TYPE
            );
        }
        
        $this->elements[$name]->setValue($value);
        
        return $this;
    }
    
   /**
    * Retrieves a hidden element by its name, if it exists.
    * 
    * @param string $name
    * @return UI_Form_Element_Hidden|NULL
    */
    public function getHidden(string $name) : ?UI_Form_Element_Hidden
    {
        $el = $this->getElementByName($name);
        
        if($el instanceof UI_Form_Element_Hidden) {
            return $el;
        }
        
        return null;
    }
    
    public function getElementByName(string $name) : ?UI_Form_Element
    {
        if(isset($this->elements[$name])) {
            return $this->elements[$name];
        }
        
        return null;
    }
    
    public function addButton(string $html) : UI_Form
    {
        $this->buttons[] = $html;
        return $this;
    }
    
    public function hasContent() : bool
    {
        return !empty($this->elements);
    }
    
    protected function _render() : string
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
    
    public function isSubmitted() : bool
    {
        return $this->request->getBool($this->getSubmitVarName());
    }
    
    public function getSubmitVarName() : string
    {
        return $this->id.'_save';
    }
    
    public function validate() : bool
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
    
    public function addHTML(string $html) : UI_Form_Element_HTML
    {
        $this->htmlCounter++;
        
        $name = 'form_html_'.$this->htmlCounter; 
        
        $el = new UI_Form_Element_HTML($this, $name);
        $el->setHTML($html);
        
        $this->elements[$name] = $el;
        
        return $this;
    }
    
   /**
    * @var UI_Form_Element[]
    */
    protected $elements;
    
    public function addSelect(string $name, string $label) : UI_Form_Element_Select
    {
        $el = new UI_Form_Element_Select($this, $name);
        $el->setLabel($label);
        
        $this->elements[$name] = $el;
        
        return $el;
    }
    
    protected $headerCount = 0;
    
    public function addHeader(string $label) : UI_Form_Element_Header
    {
        $this->headerCount++;
        
        $name = 'form_header_'.$this->headerCount;
        
        $el = new UI_Form_Element_Header($this, $name);
        $el->setLabel($label);
        
        $this->elements[$name] = $el;
        
        return $el;
    }

    public function addString(string $name, string $label) : UI_Form_Element_String
    {
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
    public function addText(string $name, string $label) : UI_Form_Element_Text
    {
        $el = new UI_Form_Element_Text($this, $name);
        $el->setLabel($label);

        $this->elements[$name] = $el;
        
        return $el;
    }
    
    public function getValues() : array
    {
        $values = array();
        
        foreach($this->elements as $element) 
        {
            if($element->getName() === $this->getSubmitVarName()) {
                continue;
            }
            
            if($element->supportsValue()) {
                $values[$element->getName()] = $element->getValue();
            }
        }
        
        return $values;
    }
}