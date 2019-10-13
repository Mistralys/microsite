<?php

declare(strict_types=1);

namespace Microsite;

class UI_Template_DataGrid extends UI_Template
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
        $header = $this->ui->createTemplate('DataGrid/Header')
        ->setVar('grid', $this->grid);
        
        $body = $this->ui->createTemplate('DataGrid/Body')
        ->setVar('grid', $this->grid);
        
        $footer = $this->ui->createTemplate('DataGrid/Footer')
        ->setVar('grid', $this->grid);
        
        ob_start();
        
        ?>
            <table class="<?php echo $this->grid->getClassesAsString() ?>">
            	<?php 
            	   echo $header->render();
            	   echo $body->render();
            	   echo $footer->render();
        	    ?>
            </table>
        <?php
            
        return ob_get_clean();
    }
}
