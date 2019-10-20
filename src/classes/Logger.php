<?php

declare(strict_types=1);

namespace Microsite;

class Logger
{
    public static function log(string $message, string $prefix='') : void
    {
        if(is_array($message)) 
        {
            $message = nl2br(json_encode($message));
        }
        
        if(!empty($prefix)) {
            $prefix .= ' | ';
        }
        
        echo '<div style="font-family:monospace;">'.date('i:s').' | '.$prefix.$message.'</div>';
    }
    
    protected static $separator;
    
    public static function logHeader(string $header) : void
    {
        if(!isset(self::$separator)) {
            self::$separator = str_repeat('-', 70);
        }
        
        self::log(self::$separator);
        self::log(mb_strtoupper($header));
        self::log(self::$separator);}
}