<?php

namespace Microsite;

abstract class UI_Form_Validation
{
    protected $element;
    
    protected $message;
    
    public function __construct(UI_Form_Element $element, $message)
    {
        $this->element = $element;
        $this->message = $message;
    }
    
    public function getMessage()
    {
        return $this->message;
    }
    
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
    
    abstract public function validate($value);
}