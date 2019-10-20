<?php

declare(strict_types=1);

namespace Microsite;

class UI_DataGrid_Column implements \AppUtils\Interface_Optionable
{
    use \AppUtils\Traits_Optionable;
    
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
    
    public function __construct(UI_DataGrid $grid, string $dataKey, string $label)
    {
        $this->grid = $grid;
        $this->name = $dataKey;
        $this->label = $label;
    }
    
    public function getName() : string
    {
        return $this->name;
    }

    public function getLabel() : string
    {
        return $this->label;
    }
    
    public function alignLeft() : UI_DataGrid_Column
    {
        return $this->setAlign('left');
    }
    
    public function alignRight() : UI_DataGrid_Column
    {
        return $this->setAlign('right');
    }
    
    public function alignCenter() : UI_DataGrid_Column
    {
        return $this->setAlign('center');
    }
    
    public function setAlign(string $value) : UI_DataGrid_Column
    {
        $this->setOption('align', $value);
        return $this;
    }
    
    public function getDefaultOptions(): array
    {
        return array(
            'align' => 'left'
        );
    }
    
    public function filterValue($value)
    {
        // FIXME implement filtering
        return $value;
    }
}