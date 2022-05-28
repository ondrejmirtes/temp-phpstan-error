<?php
/**
 * 
 * @param mixed $var
 */
function dbg($var):void{
    \ZimLogger\MainZim::$CurrentLogger->debug($var);
}

/**
 * 
 * @param mixed $var
 */
function dbgd($var):void{
    dbg($var);
    die;
}

/**
 * @param string $n
 */
function dbgn(string $n):void{
    dbg('===================' . $n . '===================');
}

/**
 * @param string $n
 */
function dbgnd(string $n):void{
    dbgn($n);
    die;
}

/**
 * @param string $n
 * @param mixed $var
 */
function dbgr(string $n,$var):void{
    dbgn($n);
    dbg($var);
}

/**
 * @param string $n
 * @param mixed $var
 */
function dbgrd(string $n,$var):void{
    dbgr($n,$var);
    die;
}

/**
 * @param mixed $inp
 */
function debug($inp):void {
    \ZimLogger\MainZim::$CurrentLogger->debug($inp);
}

/**
 * @param mixed $inp
 * @param bool $full_stack
 */
function info($inp,bool $full_stack=false):void {
    \ZimLogger\MainZim::$CurrentLogger->info($inp, $full_stack);
}

/**
 * @param mixed $inp
 * @param bool $full_stack
 */
function warning($inp,bool $full_stack=false):void {
    \ZimLogger\MainZim::$CurrentLogger->warning($inp, $full_stack);
}

/**
 * @param mixed $inp
 * @param bool $full_stack
 */
function error($inp,bool $full_stack=false):void{
    \ZimLogger\MainZim::$CurrentLogger->error($inp, $full_stack);
}

/**
 * @param mixed $inp
 * @param bool $full_stack
 */
function fatal($inp,bool $full_stack=false):void{
    \ZimLogger\MainZim::$CurrentLogger->fatal($inp, $full_stack);
}
