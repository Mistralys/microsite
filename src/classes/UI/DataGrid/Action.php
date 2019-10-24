<?php

declare(strict_types=1);

namespace Microsite;

class UI_DataGrid_Action
{
    const ERROR_INVALID_CALLBACK = 40701;
    
   /**
    * @var UI_DataGrid
    */
    protected $grid;
    
   /**
    * @var string
    */
    protected $name;
    
   /**
    * @var string
    */
    protected $label;
    
   /**
    * @var callable
    */
    protected $callback;
    
   /**
    * @param UI_DataGrid $grid
    * @param string $name
    * @param string $label
    * @param callable $callback
    * @throws Exception If an invalid callback is specified.
    * 
    * @see UI_DataGrid_Action::ERROR_INVALID_CALLBACK
    */
    public function __construct(UI_DataGrid $grid, string $name, string $label, $callback)
    {
        $this->grid = $grid;
        $this->name = $name;
        $this->label = $label;
        
        $this->setCallback($callback);
    }
    
   /**
    * Sets the callback for the action, overwriting the 
    * existing one.
    * 
    * @param callable $callback
    * @throws Exception
    * @return UI_DataGrid_Action
    */
    public function setCallback($callback) : UI_DataGrid_Action
    {
        if(is_callable($callback)) {
            $this->callback = $callback;
            return $this;
        }
        
        throw new Exception(
            sprintf(
                'Invalid callback for data grid [%s] and action [%s].', 
                $this->grid->getID(),
                $this->getName()
            ),
            null,
            self::ERROR_INVALID_CALLBACK
        );
    }
    
    public function getName() : string
    {
        return $this->name;
    }
    
    public function getLabel() : string
    {
        return $this->label;
    }
    
    public function getGrid() : UI_DataGrid
    {
        return $this->grid;
    }
    
    public function getSelectedValues() : array
    {
        return $this->grid->getSelectedValues();
    }
    
    public function start() : void
    {
        if(!$this->grid->isSubmitted()) {
            return;
        }
        
        call_user_func($this->callback, $this);
    }
}
