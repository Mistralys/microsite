<?php

declare(strict_types=1);

namespace Microsite;

class UI_Template_Page extends UI_Template
{
   /**
    * @var UI_Breadcrumb
    */
    protected $breadcrumb;
    
   /**
    * @var Page
    */
    protected $targetPage;
    
    protected function init()
    {
        $this->breadcrumb = $this->getVar('breadcrumb');
        $this->targetPage = $this->getVar('page');
    }
    
    public function _render() : string
    {
        ob_start();

        $this->breadcrumb->display();
        
        $this->createTemplate('Subnav')
        ->setVar('navigation', $this->targetPage->getNavigation())
        ->display();
        
        $title = $this->targetPage->getPageTitle();
        if(!empty($title)) 
        {
            ?>
            	<h2><?php echo $title ?></h2>
        	<?php 
        }

        $abstract = $this->targetPage->getPageAbstract();
        if(!empty($abstract))
        {
            ?>
            	<p><?php echo $abstract ?></p>
            	<hr>
        	<?php
        }
        
        echo $this->getVar('content');
        
        return ob_get_clean();
    }
}
