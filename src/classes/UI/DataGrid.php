<?php

declare(strict_types=1);

namespace Microsite;

/**
 * HTML table renderer: provides an easy to use API to configure the
 * grid, and it handles all the management and rendering.
 *
 * @package Microsite
 * @subpackage UI
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class UI_DataGrid implements Interface_Renderable, Interface_Classable
{
    use Traits_Renderable;
    use Traits_Classable;
    
    const ERROR_COLUMN_ALREADY_EXISTS = 39701;
    
    const ERROR_CANNOT_ADD_COLS_AFTER_ROWS = 39702;
    
    const ERROR_CANNOT_ADD_ROW_BEFORE_COLUMNS = 39703;
    
    const ERROR_CANNOT_RENDER_WITHOUT_COLUMNS = 39704;
    
   /**
    * @var UI
    */
    protected $ui;
    
   /**
    * @var UI_DataGrid_Column[]
    */
    protected $columns = array();
    
   /**
    * @var UI_DataGrid_Row[]
    */
    protected $rows = array();
    
    public function __construct(UI $ui)
    {
        $this->ui = $ui;
        
        $this->addClass('table');
    }
   
   /**
    * Adds a new column to the grid, and returns the instance
    * to configure it further as needed.
    * 
    * @param string $dataKey The name of the data key to fetch the value from in the data sets.
    * @param string $label Human readable label shown in the table header.
    * @return UI_DataGrid_Column
    * @throws Exception
    */
    public function addColumn(string $dataKey, string $label) : UI_DataGrid_Column
    {
        if(!empty($this->rows)) 
        {
            throw new Exception(
                'Cannot add columns after adding rows',
                'It is not possible to add additional columns to the grid when rows have been added.',
                self::ERROR_CANNOT_ADD_COLS_AFTER_ROWS
            );
        }
        
        $column = new UI_DataGrid_Column($this, $dataKey, $label);

        if(isset($this->columns[$dataKey])) 
        {
            throw new Exception(
                'Column already exists',
                sprintf(
                    'Cannot add column [%s]: it has already been added.',
                    $dataKey
                ),
                self::ERROR_COLUMN_ALREADY_EXISTS
            );
        }
        
        $this->columns[$dataKey] = $column;
        
        return $column;
    }
    
   /**
    * Retrieves all columns that were added to the grid.
    * @return \Microsite\UI_DataGrid_Column[]
    */
    public function getColumns()
    {
        return $this->columns;
    }
    
   /**
    * Adds a new row to the grid. There are several ways to specify
    * the data set for the row:
    * 
    * <ol>
    * <li>As parameters of the method, in the order of the columns</li>
    * <li>As an array with the values, in the order of the columns</li>
    * <li>As an associative array with dataKey => value pairs</li>
    * <li>After adding the row using its setColumnValue method</li>
    * </ol>
    * 
    * NOTE: Columns must have been added before adding rows.
    * 
    * @return \Microsite\UI_DataGrid_Row
    * @throws Exception
    */
    public function addRow()
    {
        if(empty($this->columns))
        {
            throw new Exception(
                'Cannot add rows without columns.',
                'Cannot add rows when no columns have been added first.',
                self::ERROR_CANNOT_ADD_ROW_BEFORE_COLUMNS
            );
        }
        
        $values = func_get_args();
        if(count($values) == 1 && is_array($values[0])) {
            $values = $values[0];
        }
        
        $entry = new UI_DataGrid_Row($this, $values);
        
        $this->rows[] = $entry;
        
        return $entry;
    }
    
    public function hasRows() : bool
    {
        return !empty($this->rows);
    }
    
    public function hasColumns() : bool
    {
        return !empty($this->columns);
    }
    
    protected function preRender()
    {
        if(!$this->hasColumns()) 
        {
            throw new Exception(
                'Cannot render a grid without columns',
                null,
                self::ERROR_CANNOT_RENDER_WITHOUT_COLUMNS
            );
        }
    }
    
    protected function _render() : string
    {
        return $this->ui->createTemplate('DataGrid')
        ->setVar('grid', $this)
        ->render();
    }
    
   /**
    * Checks whether the column exists by data key name.
    * @param string $name
    * @return bool
    */    
    public function columnExists(string $dataKey) : bool
    {
        return isset($this->columns[$dataKey]);
    }
    
   /**
    * @return \Microsite\UI_DataGrid_Row[]
    */
    public function getRows()
    {
        return $this->rows;
    }
}