<?php

declare(strict_types=1);

namespace Microsite;

class UI_Template_Subnav extends UI_Template
{
   /**
    * @var UI_Navigation
    */
    protected $navigation;
    
   /**
    * @var UI_Navigation_Item[]
    */
    protected $items;
    
    protected function init()
    {
        $this->navigation = $this->getVar('navigation');
        $this->items = $this->navigation->getItems();
    }
    
    public function _render() : string
    {
        if(!$this->navigation->hasItems()) {
            return '';
        }

        ob_start();
        
        ?>
            <ul class="nav nav-pills">
            	<?php 
                    foreach($this->items as $item)
                    {
                        $active = '';
                        if($item->isActive()) {
                            $active = 'active';
                        }
                        
                        ?>
                            <li role="presentation" class="<?php $active ?>">
                            	<a href="<?php echo $item->getURL() ?>"><?php echo $item->getLabel() ?></a>
                        	</li>
                    	<?php 
                    }
                ?>
            </ul>
            <br/>
        <?php
        
        return ob_get_clean();
    }
}
