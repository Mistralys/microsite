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
    
   /**
    * @var integer
    */
    protected $number = 0;
    
    public function __construct(UI_DataGrid $grid, int $number, array $data=array())
    {
        $this->grid = $grid;
        $this->data = $data;
        $this->number = $number;
        
        $this->initCells();
    }
    
    public function getNumber() : int
    {
        return $this->number;
    }
    
    protected function initCells()
    {
        $this->log('Initializing cells.');
        
        $cols = array_values($this->grid->getColumns());
        $total = count($cols);
        
        for($i=0; $i < $total; $i++) 
        {
            $col = $cols[$i];
            $value = $this->data[$i];
            
            $cell = new UI_DataGrid_Row_Cell($this, $col);
            $cell->setValue($value);
            
            $this->cells[$col->getName()] = $cell; 
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
        $this->log('Rendering the row.');
        
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
    
    protected function log($message) : void
    {
        $this->grid->log(sprintf(
            'Row [%03d] | %s',
            $this->getNumber(),
            $message
        ));
    }

}