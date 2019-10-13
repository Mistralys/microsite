<?php


    // your site's classes namespace - required for the microsite to work.
    $siteNS = 'YourNamespace';
    
    // the URL to access the website - can be a local address like localhost.
    $siteURL = 'http://website.com';


    
    // ---------------------------------------------------------
    // DO NOT CHANGE ANYTHING BELOW
    // ---------------------------------------------------------

    $autoload = __DIR__.'/vendor/autoload.php';
    if(!file_exists($autoload)) {
        die('<b>ERROR:</b> The composer autoloader is not present, please run composer first.');
    }
    
    require_once $autoload;
    
    $site = call_user_func(
        array('\\'.$siteNS.'\Site', 'boot'),
        $siteNS,
        __DIR__,
        $siteURL
    );
    
    $site->display();
