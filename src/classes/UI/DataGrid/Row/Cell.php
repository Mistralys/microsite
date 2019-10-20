<?php

declare(strict_types=1);

namespace Microsite;

class UI_DataGrid_Row_Cell implements Interface_Renderable, Interface_Classable
{
    use Traits_Renderable;
    use Traits_Classable;
    
   /**
    * @var UI_DataGrid_Row
    */
    protected $row;
    
   /**
    * @var UI_DataGrid_Column
    */
    protected $column;
    
   /**
    * @var mixed
    */
    protected $value;
    
    public function __construct(UI_DataGrid_Row $row, UI_DataGrid_Column $column)
    {
        $this->row = $row;
        $this->column = $column;
    }
    
    public function getName() : string
    {
        return $this->column->getName();
    }
    
   /**
    * Sets the value of the cell.
    * 
    * @param mixed $value
    * @return UI_DataGrid_Row_Cell
    */
    public function setValue($value) : UI_DataGrid_Row_Cell
    {
        $this->value = $value;
        return $this;
    }
    
    protected function _render() : string
    {
        ob_start();
        
        ?>
        	<td<?php echo $this->getClassAttribute() ?>>
        		<?php echo $this->column->filterValue($this->value) ?>
        	</td>
        <?php
        
        return ob_get_clean();
    }
}