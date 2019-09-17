<?php

namespace Microsite;

abstract class UI_Form_Element
{
   /**
    * @var UI_Form
    */
    protected $form;
    
   /**
    * @var string
    */
    protected $name;
    
   /**
    * @var string
    */
    protected $id;
    
    protected $label;
    
    protected $value;
    
    protected $filters;
    
    protected $attributes;
    
    protected $classes = array();
    
    protected $description;
    
    public function __construct(UI_Form $form, string $name)
    {
        $this->form = $form;
        $this->name = $name;
        
        $this->init();
    }
    
    protected function init()
    {
        
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }
    
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    public function render()
    {
        if($this->errorMessage) {
            $this->addClass('is-invalid');
        }
        
        $elHTML = $this->_render();

        $html = '';
        
        if($this->supportsWrapper()) 
        {
            $label = $this->label;
            
            if($this->isRequired()) {
                $label .= ' <i class="fa fa-exclamation-triangle text-danger"></i>';
            }
            
            $html = 
            '<div class="form-group">'.
    			'<label>'.$label.'</label>'.
    			$elHTML;
    			if(!empty($this->description)) {
    			    $html .= 
    			    '<p class="help-block">'.$this->description.'</p>';
    			}
    			if($this->errorMessage) {
    			    $html .= 
    			    '<div class="invalid-feedback">'.$this->errorMessage.'</div>';
    			}
    			$html .=
    		'</div>';
        }
        else 
        {
            $html = $elHTML;
        }
			
		return $html;
    }
    
    abstract protected function _render();
    
    public function getValue()
    {
        if(!$this->supportsValue()) {
            return null;
        }
        
        $value = $this->value;
        
        if($this->form->isSubmitted() && isset($_REQUEST[$this->name])) {
            $value = $_REQUEST[$this->name];
        }
        
        $value = $this->filterValue($value);
        
        return $value;
    }
    
    protected function filterValue($value)
    {
        return $value;
    }
    
    public function filter($callback)
    {
        if(!$this->supportsValue()) {
           return $this;
        }
        
        if(!is_callable($callback)) {
            throw new \Exception('Not a valid callback for element '.$this->getElementType().'['.$this->name.']');
        }
        
        $this->filters[] = $callback;
        
        return $this;
    }
    
    public function filterTrim()
    {
        return $this->filter('trim');
    }
    
    public function getElementType()
    {
        return str_replace('UI_Form_Element_', '', get_class($this));
    }
    
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }
    
    protected function renderAttributes()
    {
        $atts = $this->attributes;
        $atts['name'] = $this->name;
        $atts['id'] = $this->id;
        $atts['class'] = $this->renderClasses();
        
        return AppUtils\ConvertHelper::array2attributeString($atts);
    }
    
    protected function renderClasses()
    {
        return implode(' ', $this->classes);
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
        return $this->name;
    }
    
    public function getID()
    {
        return $this->id;
    }
    
    public function setID($id)
    {
        $this->id = $id;
        return $this;
    }
    
    public function addClass($class)
    {
        if(!in_array($class, $this->classes)) {
            $this->classes[] = $class;
        }
        
        return $this;
    }
    
    public function setDescription($descr)
    {
        $this->description = $descr;
        return $this;
    }
    
    abstract public function supportsValue();
    
    abstract public function supportsWrapper();
    
    protected $valid;
    
    public function validate()
    {
        if(isset($this->valid)) {
            return $this->valid;
        }
        
        if(!$this->supportsValue()) {
            return $this->setValid();
        }
        
        $value = $this->getValue();
        
        // empty value
        if($value===null || $value==='') 
        {
            if($this->isRequired()) {
                return $this->setError('May not be empty.');
            }
         
            return $this->setValid();
        }
        
        if(!empty($this->validations)) 
        {
            foreach($this->validations as $validation) 
            {
                if(!$validation->validate($value)) {
                    return $this->setError($validation->getMessage());
                }
            }
        }
        
        return $this->setValid();
    }
    
    protected function setValid()
    {
        $this->valid = true;
        return $this->valid;
    }
    
    protected function setError($message)
    {
        $this->valid = false;
        $this->errorMessage = $message;
        return $this->valid;
    }
    
    protected $errorMessage;
    
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
    
   /**
    * @var UI_Form_Validation[]
    */
    protected $validations = array();
    
    protected $required = false;
    
    public function setRequired($required=true)
    {
        if($this->supportsValue()) {
            $this->required = $required;
        }
        
        return $this;
    }
    
    public function isRequired()
    {
        return $this->required;
    }
    
    public function validateRegex($regex, $message)
    {
        require_once 'Form/Validation/Regex.php';
        
        $valid = new UI_Form_Validation_Regex($this, $message, $regex);
        $this->validations[] = $valid;
        
        return $this;
    }
}