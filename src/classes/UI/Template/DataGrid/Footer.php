<?php

declare(strict_types=1);

namespace Microsite;

class UI_Template_DataGrid_Footer extends UI_Template
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
        ob_start();
        
        ?>
            <tfoot>
            	<?php 
            	    
            	?>
            </tfoot>
        <?php
            
        return ob_get_clean();
    }
}
