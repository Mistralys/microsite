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
        
        $this->createTemplate('Subnav')
        ->setVar('navigation', $this->targetPage->getNavigation())
        ->display();
        
        $abstract = $this->getPageAbstract();
        if(!empty($abstract)) 
        {
            ?>
            	<p><?php echo $abstract ?></p>
            	<hr>
        	<?php
        }
        
        $title = $this->getPageTitle();
        if(!empty($title)) 
        {
            ?>
            	<h2><?php echo $title ?></h2>
        	<?php 
        }
        
        $this->breadcrumb->display();
        
        echo $this->getVar('content');
        
        return ob_get_clean();
    }
}
