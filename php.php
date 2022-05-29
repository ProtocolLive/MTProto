<?php
declare(strict_types = 1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('error_reporting', '-1');
ini_set('html_errors', '1');
ini_set('max_execution_time', '-1');
date_default_timezone_set('America/Sao_Paulo');
ini_set('error_log', __DIR__ . '/error.log');

//ob_start();
//set_error_handler('vdd');

function vd($var){
  if(php_sapi_name() !== 'cli'):
    echo '<pre>';
  endif;
  var_dump($var);
  debug_print_backtrace();
  //file_put_contents(__DIR__ . '/logs/debug.log', ob_get_contents());
  if(php_sapi_name() !== 'cli'):
    echo '</pre>';
  endif;
}

function vdd($var){
  vd($var);
  die();
}