<?php

require_once __DIR__.'prepend.php';

$migrator = createMigrator();

$action = 'overview';

if(isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
}

ob_start();

try{
    $page = ucfirst($action);
    $pageFile = realpath('assets/classes/Page/'.$page.'.php');
    if(file_exists($pageFile)) 
    {
        require_once $pageFile;
        $pageClass = 'Page_'.$page;
        $page = new $pageClass();
        $page->display();
    }
    else
    {
        $method = 'render_'.$action;
        $method();
    }
}
catch(Exception $e)
{
    echo 
    '<div class="alert alert-danger">'.
        '<b>Exception</b>: '.$e->getMessage().'<br>'.
        'Source: '.$e->getFile().' line '.$e->getLine().'<br>'.
        '<br>';
        if(method_exists($e, 'getDetails')) {
            echo $e->getDetails();
        }
        echo
    '</div>'.
    '<pre>'.$e->getTraceAsString().'</pre>';
}

$content = ob_get_clean();

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>App Migrator</title>
    <link rel="icon" type="image/ico" href="favicon.ico">
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/fortawesome/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/ui.css" rel="stylesheet">
    <script src="vendor/components/jquery/jquery.min.js"></script>
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php 
        echo UI::renderHead();
    ?>
  </head>
  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="?">App Migrator</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="<?php menuActive('overview') ?>"><a href="?action=overview">Overview</a></li>
            <li class="<?php menuActive('appconfigs') ?>"><a href="?action=appconfigs">App configs</a></li>
            <li class="<?php menuActive('envcheck') ?>"><a href="?action=envcheck">Environment check</a></li>
            <li class="<?php menuActive('errorcodes') ?>"><a href="?action=errorcodes">Error codes</a></li>
            <li class="<?php menuActive('docstrap') ?>"><a href="?action=docstrap">JSDoc Management</a></li>
            <li><a href="./appwiki" target="_blank">Appwiki</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container" style="margin-top:80px;">
        <?php
            if(UI::hasMessages()) 
            {
                $messages = UI::getMessages();
                UI::clearMessages();
                foreach($messages as $message) {
                    echo 
                    '<div class="alert alert-'.$message['type'].'">'.
                        $message['text'].
                    '</div>';
                }
            }
            
            echo $content; 
        ?>
    </div>
  </body>
</html>