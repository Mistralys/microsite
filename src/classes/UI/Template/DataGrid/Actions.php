<?php

declare(strict_types=1);

namespace Microsite;

class UI_Template_DataGrid_Actions extends UI_Template
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
            <div class="form-row align-items-center">
            	<div class="col-auto">
                	<select name="action" class="form-control mb-2">
                		<option value=""><?php \AppLocalize\pt('With selected...') ?></option>
                		<?php 
                		    $actions = $this->grid->getActions();
                		
                		    foreach($actions as $action)
                		    {
                		        ?>
                		        	<option value="<?php echo $action->getName() ?>">
                		        		<?php echo $action->getLabel() ?>
            		        		</option>
                		        <?php 
                		    }
                		?>
                	</select>
            	</div>
            	<div class="col-auto">
                	<button type="submit" name="confirm" class="btn btn-secondary mb-2">
                		<?php \AppLocalize\pt('OK') ?>
                	</button>
            	</div>
            </div>
        <?php
            
        return ob_get_clean();
    }
}
