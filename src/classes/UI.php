<?php

namespace Microsite;

class UI
{
    protected static $jsHead = array();
    
    protected static $jsOnload = array();
    
    protected static $scripts = array();
    
    public static function addJSHead($statement)
    {
        self::$jsHead[] = rtrim($statement, ';');
    }
    
    public static function addJSOnload($statement)
    {
        self::$jsOnload[] = rtrim($statement, ';');
    }
    
    public static function addScript($filename)
    {
        if(!in_array($filename, self::$scripts)) {
            self::$scripts[] = $filename;
        }
    }
    
    public static function renderHead()
    {
        $lines = array();
        foreach(self::$scripts as $script) {
            $lines[] = '<script src="js/'.$script.'"></script>';
        }
        
        $lines[] = '<script>';
        $lines[] = implode(';'.PHP_EOL, self::$jsHead).';';
        $lines[] = '$(document).ready(function() {';
        $lines[] = implode(';'.PHP_EOL, self::$jsOnload);
        $lines[] = '});';
        $lines[] = '</script>';
            
        return implode(PHP_EOL, $lines);
    }
}