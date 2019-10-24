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
        if($this->grid->getOption('hover') === true) {
            $this->grid->addClass('table-hover');
        }
        
        ob_start();
        
        ?>
        	<form name="<?php echo $this->grid->getFormName() ?>" id="<?php echo $this->grid->getID() ?>">
        		<div class="form-hiddens">
        			<?php 
        			    $vars = $this->grid->getRequestVars();
        			    
        			    foreach($vars as $name => $value) {
        			        ?>
        			        	<input type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>"/>
        			        <?php 
        			    }
        			?>
        		</div>
                <table class="<?php echo $this->grid->getClassesAsString() ?>">
                	<?php
                	   echo $this->renderGridTemplate('Header');
                	   echo $this->renderGridTemplate('Body');
                	   echo $this->renderGridTemplate('Footer');
            	    ?>
                </table>
                <?php
                    if($this->grid->hasActions())
                    {
                        echo $this->renderGridTemplate('Actions');
                    }
                ?>
            </form>
        <?php
            
        $html = ob_get_clean();
        
        return $html;
    }
    
    protected function renderGridTemplate(string $name) : string
    {
        return $this->ui->createTemplate('DataGrid/'.$name)
        ->setVar('grid', $this->grid)
        ->render();
    }
}
