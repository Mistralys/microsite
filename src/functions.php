<?php 

declare(strict_types=1);

namespace Microsite;

/**
 * Creates a request-unique javascript or HTML element ID.
 * @return string
 */
function nextJSID() : string
{
    static $counter = 0;
    
    $counter++;
    
    return 'E'.$counter;
}
