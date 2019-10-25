<?php

declare(strict_types=1);

namespace Microsite;

class UI_Template_Document extends UI_Template
{
    protected function init()
    {
        $this->ui->addVendorScript('twbs/bootstrap', 'dist/css/bootstrap.min.css');
        $this->ui->addVendorScript('fortawesome/font-awesome', 'css/font-awesome.css');
        $this->ui->addSiteScript('css/ui.css');
        $this->ui->addSiteScript('js/ajax.js');
        $this->ui->addVendorScript('components/jquery', 'jquery.min.js');
        $this->ui->addVendorScript('twbs/bootstrap', 'dist/js/bootstrap.bundle.min.js');
    }
    
    public function _render() : string
    {
        ob_start();
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->site->getDocumentTitle() ?></title>
    <link rel="icon" type="image/ico" href="favicon.ico">
    <?php 
        echo $this->ui->renderHead();
    ?>
  </head>
  <body>
  	<header>
    <nav class="navbar navbar-dark navbar-expand-md fixed-top bg-dark">
      <a class="navbar-brand" href="<?php echo $this->site->getDefaultPage()->buildURL() ?>">
        <?php echo $this->site->getNavigationTitle() ?>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
  		<span class="navbar-toggler-icon"></span>
	  </button>
      <div id="navbar" class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
          	<?php 
          	    $navItems = $this->site->getNavigation()->getItems();
          	    
          	    foreach($navItems as $item) 
          	    {
          	        $class = '';
          	        if($item->isActive()) {
          	            $class = 'active';
          	        }
          	        
          	        ?>
          	        	<li class="nav-item <?php echo $class ?>">
          	        		<a href="<?php echo $item->getURL() ?>" class="nav-link"><?php echo $item->getLabel() ?></a>
      	        		</li>
          	        <?php 
          	    }
          ?>
        </ul>
      </div>
    </nav>
    </header>
	<main role="main">
        <?php
            if($this->site->hasMessages()) 
            {
                $messages = $this->site->getMessages();
                
                foreach($messages as $message) {
                    echo 
                    '<div class="alert alert-'.$message->getType().'">'.
                        $message->getIcon().' '.
                        $message->getMessage().
                    '</div>';
                }
                
                $this->site->clearMessages();
            }
            
            echo $this->getVar('page-content'); 
        ?>
    </main>
  </body>
</html>
		<?php
		return ob_get_clean();
    }
}
    