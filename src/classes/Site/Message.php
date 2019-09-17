<?php

namespace Microsite;

class Site_Message
{
    const MESSAGE_TYPE_SUCCESS = 'success';
    
    const MESSAGE_TYPE_ERROR = 'error';
    
    const MESSAGE_TYPE_INFO = 'info';
    
    protected $type;
    
    protected $message;
    
    public function __construct($type, $message)
    {
        $this->type = $type;
        $this->message = $message;
    }
    
    public function getMessage()
    {
        return $this->message;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function isError()
    {
        return $this->isType('error');
    }
    
    public function isWarning()
    {
        return $this->isType('warning');
    }
    
    public function isType($type)
    {
        return $this->type === $type;
    }
    
    public function getIcon()
    {
        if($this->isError()) {
            return '<i class="fa fa-times"></i>';
        }
        
        if($this->isWarning()) {
            return '<i class="fa fa-times"></i>';
        }
        
        return '';
    }
    
    public function getTextClass()
    {
        if($this->isError()) {
            return 'text-danger';
        }
        
        if($this->isWarning()) {
            return 'text-warning';
        }
        
        return '';
    }
    
    public function render()
    {
        return sprintf(
            '<div class="%s">'.
                '%s %s'.
            '</div>',
            $this->getTextClass(),
            $this->getIcon(),
            $this->getMessage()
        );
    }
}