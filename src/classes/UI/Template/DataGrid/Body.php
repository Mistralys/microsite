<?php

declare(strict_types=1);

namespace Microsite;

class UI_Template_DataGrid_Body extends UI_Template
{
    /**
     * @var UI_DataGrid
     */
    protected $grid;
    
    protected function init()
    {
        $this->grid = $this->getVar('grid');
    }
    
    public function _render() : string
    {
        $rows = $this->grid->getRows();
        
        ob_start();
        
        ?>
            <tbody>
            	<?php 
                	foreach($rows as $row) {
                	    $row->display();
                	}
            	?>
            </tbody>
        <?php
            
        return ob_get_clean();
    }
}
