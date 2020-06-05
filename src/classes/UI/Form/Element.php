<?php

declare(strict_types=1);

namespace Microsite;

use AppUtils\OperationResult;
use AppUtils\Traits_Classable;
use AppUtils\Interface_Classable;

abstract class UI_Form_Element implements Interface_Renderable, Interface_Classable
{
    use Traits_Renderable;
    use Traits_Classable;

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
    
    /**
     * @var UI_Form_Validation[]
     */
    protected $validations = array();
    
    /**
     * @var boolean
     */
    protected $required = false;
    
    public function __construct(UI_Form $form, string $name)
    {
        $this->form = $form;
        $this->name = $name;
        
        $this->init();
    }
    
    abstract protected function _renderElement();
    abstract public function supportsValue();
    abstract public function supportsWrapper();
    
    protected function init()
    {
        
    }

    public function setLabel(string $label)
    {
        $this->label = $label;
        return $this;
    }
    
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    
    protected function _render() : string
    {
        if($this->errorMessage) {
            $this->addClass('is-invalid');
        }
        
        $elHTML = $this->_renderElement();

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
    
    public function setAttribute(string $name, string $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }
    
    public function getAttribute(string $name)
    {
        if(isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        
        return null;
    }
    
    protected function renderAttributes()
    {
        $atts = $this->attributes;
        $atts['name'] = $this->name;
        $atts['id'] = $this->id;
        $atts['class'] = $this->renderClasses();
        
        return \AppUtils\ConvertHelper::array2attributeString($atts);
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
    
    public function setDescription(string $descr) : UI_Form_Element
    {
        $this->description = $descr;
        return $this;
    }
    
    public function validate() : OperationResult
    {
        $result = new OperationResult($this);
        
        if(!$this->supportsValue()) 
        {
            return $result;
        }
        
        $value = $this->getValue();
        
        // empty value
        if($value===null || $value==='') 
        {
            if($this->isRequired()) 
            {
                $result->makeError(t('May not be empty.'));
            }
         
            return $result;
        }
        
        $this->executeValidations($value, $result);
        
        return $result;
    }
    
    protected function executeValidations($value, OperationResult $result) : void
    {
        if(empty($this->validations))
        {
            return;
        }
        
        foreach($this->validations as $validation)
        {
            if(!$validation->validate($value))
            {
                $result->makeError($validation->getMessage());
                return;
            }
        }
    }
    
    protected function setValid() : bool
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
    
    public function setRequired($required=true)
    {
        if($this->supportsValue()) {
            $this->required = $required;
        }
        
        return $this;
    }
    
    public function isRequired() : bool
    {
        return $this->required;
    }
    
   /**
    * Validates the element using a regex on its value.
    * 
    * @param string $regex
    * @param string $message
    * @return $this
    */
    public function validateRegex(string $regex, string $message) : UI_Form_Element
    {
        $valid = new UI_Form_Validation_Regex($this, $message, $regex);
        $this->validations[] = $valid;
        
        return $this;
    }
}
