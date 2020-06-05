<?php

declare(strict_types=1);

namespace Microsite;

use AppUtils\Interface_Optionable;
use AppUtils\Traits_Optionable;
use AppUtils\Traits_Classable;
use AppUtils\Interface_Classable;

/**
 * HTML table renderer: provides an easy to use API to configure the
 * grid, and it handles all the management and rendering.
 *
 * @package Microsite
 * @subpackage UI
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class UI_DataGrid implements Interface_Renderable, Interface_Classable, Interface_Loggable, Interface_Optionable
{
    use Traits_Renderable;
    use Traits_Classable;
    use Traits_Loggable;
    use Traits_Optionable;
    
    const ERROR_COLUMN_ALREADY_EXISTS = 39701;
    
    const ERROR_CANNOT_ADD_COLS_AFTER_ROWS = 39702;
    
    const ERROR_CANNOT_ADD_ROW_BEFORE_COLUMNS = 39703;
    
    const ERROR_CANNOT_RENDER_WITHOUT_COLUMNS = 39704;
    
    const ERROR_ACTION_ALREADY_EXISTS = 39705;
    
    const ERROR_UNKNOWN_ACTION_NAME = 39706;
    
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
    
   /**
    * @var string
    */
    protected $id;
    
   /**
    * @var \AppUtils\Request
    */
    protected $request;
    
   /**
    * @var array
    */
    protected $requestVars = array();
    
   /**
    * @var UI_DataGrid_Action[]
    */
    protected $actions = array();
    
    public function __construct(UI $ui, string $id)
    {
        $this->ui = $ui;
        $this->id = $id;
        $this->request = new \AppUtils\Request();
        
        $this->log('Created new data grid.');
        
        $this->addClass('table');
    }
    
    public function getID() : string
    {
        return $this->id;
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
        
        $this->log(sprintf('Adding column [%s].', $dataKey));
        
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
        
        $number = count($this->rows) + 1;
        
        $this->log(sprintf('Adding row [%s].', $number));
        
        $entry = new UI_DataGrid_Row($this, $number, $values);
        
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
        
        $this->setRequestVar($this->getID().'_submit', 'yes');
    }
    
    protected function _render() : string
    {
        $this->log('Rending the grid: Found ['.count($this->rows).'] rows and ['.count($this->columns).'] columns.');
        
        $html = $this->ui->createTemplate('DataGrid')
        ->setVar('grid', $this)
        ->render();
        
        return $html;
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
 
    public function getLogPrefix(): string
    {
        return 'DataGrid ['.$this->getID().']';
    }
    
   /**
    * Enables the multi-selection feature: adds controls
    * to select items in the list.
    * 
    * @param bool $enable Whether to enable or disable the feature.
    * @return UI_DataGrid
    */
    public function enableMultiselect(bool $enable=true) : UI_DataGrid
    {
        $this->setOption('multiselect', $enable);
        return $this;
    }
 
    public function getDefaultOptions(): array
    {
        return array(
            'multiselect' => false,
            'hover' => false
        );
    }
    
    public function hasMultiselect() : bool
    {
        return $this->getOption('multiselect') === true;
    }
    
    public function enableHover(bool $enable=true) : UI_DataGrid
    {
        $this->setOption('hover', $enable);
        return $this;
    }
    
    public function getFormName() : string
    {
        return $this->getID().'-form';
    }
    
    public function setRequestVar(string $name, string $value) : UI_DataGrid
    {
        $this->requestVars[$name] = $value;
        return $this;
    }
    
    public function setRequestVars(array $vars) : UI_DataGrid
    {
        foreach($vars as $name => $value) 
        {
            $this->setRequestVar($name, $value);
        }
        
        return $this;
    }
    
    public function getRequestVars() : array
    {
        return $this->requestVars;
    }

   /**
    * Adds a list action that can be applied to a selection of items
    * in the list.
    * 
    * Note: automatically enables the multiselect feature.
    * 
    * @param string $name
    * @param string $label
    * @param callable $callback The callback to run when this action is submitted.
    * @throws Exception
    * @return UI_DataGrid_Action
    * 
    * @see UI_DataGrid::ERROR_ACTION_ALREADY_EXISTS
    */
    public function addAction(string $name, string $label, $callback) : UI_DataGrid_Action
    {
        $this->enableMultiselect();
        
        if(isset($this->actions[$name])) 
        {
            throw new Exception(
                sprintf('The data grid action [%s] already exists in the data grid [%s].', $name, $this->getID()),
                null,
                self::ERROR_ACTION_ALREADY_EXISTS
            );
        }
        
        $this->actions[$name] = new UI_DataGrid_Action($this, $name, $label, $callback);
        
        return $this->actions[$name];
    }
    
    public function hasActions() : bool
    {
        return $this->hasMultiselect() && !empty($this->actions);
    }
    
   /**
    * Retrieves all action instances that were added to the grid.
    * 
    * @return \Microsite\UI_DataGrid_Action[]
    */
    public function getActions() 
    {
        return $this->actions;
    }
    
    public function isSubmitted() : bool
    {
        return $this->request->getBool($this->getID().'_submit'); 
    }
    
    public function start() : void
    {
        if(!$this->isSubmitted()) {
            return;
        }
        
        $this->checkActions();
    }
    
   /**
    * Retrieves the names of all actions that have been
    * added to the grid.
    * 
    * @return string[]
    */
    public function getActionNames() : array
    {
        return array_keys($this->actions);
    }
    
   /**
    * Retrieves an action by its name.
    * 
    * @param string $name
    * @throws Exception
    * @return \Microsite\UI_DataGrid_Action
    * 
    * @see UI_DataGrid::ERROR_UNKNOWN_ACTION_NAME
    */
    public function getActionByName(string $name)
    {
        if(isset($this->actions[$name])) {
            return $this->actions[$name];
        }
        
        throw new Exception(
            sprintf('Unknown grid action [%s] in grid [%s].', $name, $this->getID()),
            null,
            self::ERROR_UNKNOWN_ACTION_NAME
        );
    }
    
    protected function checkActions()
    {
        if(!$this->hasMultiselect()) {
            return;
        }
        
         $actionName = $this->request->registerParam('action')
         ->setEnum($this->getActionNames())
         ->get();
         
         if(empty($actionName)) {
             return;
         }
         
         $this->getActionByName($actionName)->start();
    }
    
   /**
    * Retrieves a list of all values selected in the list.
    * 
    * NOTE: Will return an empty array if the multiselect
    * option is not enabled, and if the list has not been
    * submitted yet.
    * 
    * @return string[]
    */
    public function getSelectedValues() : array
    {
        if(!$this->hasMultiselect() || !$this->isSubmitted()) {
            return array();
        }
        
        $values = $this->request->registerParam('primaries')->setArray()->get();
        
        $result = array();
        $valid = $this->getPrimaryValues();
        
        // to avoid false data being submitted, we only keep the
        // values that are actually present in the list.
        foreach($values as $value)
        {
            if(in_array($value, $valid)) {
                $result[] = $value;
            }
        }
        
        return $result;
    }
    
   /**
    * Retrieves a list of all available primary values
    * in the list.
    * 
    * Note: Returns an empty array if the multiselect
    * feature has not been enabled.
    * 
    * @return string[]
    */
    public function getPrimaryValues()
    {
        if(!$this->hasMultiselect()) {
            return array();
        }
        
        $result = array();
        
        foreach($this->rows as $row) 
        {
            $value = $row->getPrimaryValue();
            
            if($value !== '' && !in_array($value, $result)) {
                $result[] = $row->getPrimaryValue();
            }
        }
        
        return $result;
    }
}
    