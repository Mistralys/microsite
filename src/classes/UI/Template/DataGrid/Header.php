<?php

declare(strict_types=1);

namespace Microsite;

class UI_Template_DataGrid_Header extends UI_Template
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
            <thead>
            	<?php 
            	    
            	?>
            </thead>
        <?php
            
        return ob_get_clean();
    }
}
