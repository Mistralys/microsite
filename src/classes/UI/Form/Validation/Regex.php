<?php

namespace Microsite;

class UI_Form_Validation_Regex extends UI_Form_Validation
{
    protected $regex;
    
    public function __construct($element, $message, $regex)
    {
        parent::__construct($element, $message);
        $this->regex = $regex;
    }
    
    public function validate($value)
    {
        return preg_match($this->regex, $value);
    }
}