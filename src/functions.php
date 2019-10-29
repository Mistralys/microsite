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

function t()
{
    return call_user_func_array('\AppLocalize\t', func_get_args());
}

function pt()
{
    return call_user_func_array('\AppLocalize\pt', func_get_args());
}

function pts()
{
    return call_user_func_array('\AppLocalize\pts', func_get_args());
}