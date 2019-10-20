<?php

declare(strict_types=1);

namespace Microsite;

class Logger
{
    const MODE_NONE = 'none';
    const MODE_ECHO = 'echo';
    
    protected static $logMode = self::MODE_NONE;
    
    public static function log(string $message, string $prefix='') : void
    {
        if(self::$logMode === self::MODE_NONE) {
            return;
        }
        
        if(is_array($message)) 
        {
            $message = nl2br(json_encode($message));
        }
        
        if(!empty($prefix)) {
            $prefix .= ' | ';
        }
        
        if(self::$logMode === self::MODE_ECHO) {
            echo '<div style="font-family:monospace;">'.date('i:s').' | '.$prefix.$message.'</div>';
        }
    }
    
    protected static $separator;
    
    public static function logHeader(string $header) : void
    {
        if(self::$logMode === self::MODE_NONE) {
            return;
        }
        
        if(!isset(self::$separator)) {
            self::$separator = str_repeat('-', 70);
        }
        
        self::log(self::$separator);
        self::log(mb_strtoupper($header));
        self::log(self::$separator);}
}