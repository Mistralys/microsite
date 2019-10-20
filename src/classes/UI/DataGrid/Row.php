<?php

declare(strict_types=1);

namespace Microsite;

class UI_DataGrid_Row implements Interface_Renderable, Interface_Classable
{
    use Traits_Renderable;
    use Traits_Classable;
    
    const ERROR_CANNOT_SET_VALUE_UNKNOWN_KEY = 39801;
    
   /**
    * @var array
    */
    protected $data;
    
   /**
    * @var UI_DataGrid
    */
    protected $grid;
    
   /**
    * @var UI_DataGrid_Row_Cell[]
    */
    protected $cells = array();
    
    public function __construct(UI_DataGrid $grid, array $data=array())
    {
        $this->grid = $grid;
        $this->data = $data;
        
        $this->initCells();
    }
    
    protected function initCells()
    {
        $cols = $this->grid->getColumns();
        
        foreach($cols as $col) 
        {
            $this->cells[$col->getName()] = new UI_DataGrid_Row_Cell($this, $col); 
        }
    }
    
   /**
    * Sets the value for a specific cell in the row.
    *  
    * @param string $dataKey
    * @param mixed $value
    * @throws Exception
    * @return UI_DataGrid_Row
    */
    public function setCellValue(string $dataKey, $value) : UI_DataGrid_Row
    {
        if(!isset($this->cells[$dataKey])) 
        {
            throw new Exception(
                'Cannot set row value for unknown cell.',
                sprintf(
                    'Cannot set row value for cell of column [%s]: the column does not exist in the data grid.',
                    $dataKey
                ),
                self::ERROR_CANNOT_SET_VALUE_UNKNOWN_KEY
            );
        }
        
        $this->cells[$dataKey]->setValue($value);
        
        return $this;
    }
    
    protected function _render() : string
    {
        ob_start();
        ?>
        	<tr>
        		<?php
            		foreach($this->cells as $cell) 
            		{
            		    echo $cell->render();
            		}
        		?>
        	</tr>
        <?php 
        
        return ob_get_clean();
    }
}