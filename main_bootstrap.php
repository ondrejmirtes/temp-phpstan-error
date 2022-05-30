<?php
ini_set('error_reporting', E_ALL|E_STRICT);
/**
 * Common config values all subdomains and CLI will be using
 */
//generic paths
define('CORE_PATH', 	    __DIR__);
define('LOG_PATH',			   '/var/log/lms2/');
define('LIB_PATH', CORE_PATH.'/lib');

//NFS master and slave paths
define('DOMAIN_PATH', 		CORE_PATH   . '/subdomains/' . SUBDOMAIN);
define('LAYOUT_PATH', 		DOMAIN_PATH . '/layouts');
define('VIEW_PATH', 		DOMAIN_PATH . '/views');

//common values/constants
define('BIN_LOGIN_ORG_ID',  -1);//an org to use when login a user in the cron system and other backend processes.
define('OVERRIDE',          -1);//a way to signal to override calculated values
define('BACKTRACE_MASK',     0);
define('FORCE_HTTPS',        1); //a value u send to the url function to force the use of https
define('DONT_FORCE_SCHEMA',  0); //If it is https it remains https, if it is http it remains http
define('PREVENT_FORCE_HTTPS',-1); //a value u send to the url function to force the use of http (prevent https)
ini_set('include_path', '.'  . 
    PATH_SEPARATOR . DOMAIN_PATH        . '/plugins' .
    PATH_SEPARATOR . CORE_PATH . '/lib'           . 
    PATH_SEPARATOR . DOMAIN_PATH        . '/controllers' .
    PATH_SEPARATOR . CORE_PATH          . '/model' 
);

//some other bootstrapping and important functions and the ENVIRONMENT definitions
require_once __DIR__ . '/vendor/director-moav/sitel/src/SiTEL/functions.php';
require_once LIB_PATH.'/commons.php';
require_once LIB_PATH.'/commons/url.php';
require_once LIB_PATH.'/commons/dates.php';
require_once LIB_PATH.'/commons/feature_control.php';
require_once __DIR__  . '/environments/itay_mac.php';

spl_autoload_register('autoload');
include CORE_PATH . '/vendor/autoload.php';
\ZimLogger\MainZim::include_shortcuts();

