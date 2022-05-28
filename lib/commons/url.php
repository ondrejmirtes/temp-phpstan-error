<?php namespace commons\url;

const DONT_FORCE_SCHEMA = 0,
      FORCE_HTTP        = 1,
      FORCE_HTTPS       = 2
;

/**
 * checks on $_SERVER if current schema is https or not
 * @return boolean
 */
function is_https(){
    \error('TOBEDELETED202142');
    return true;
    //return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');
}

/**
 * return the schema for the url according to input flags
 * @param integer $rule DONT_FORCE_SCHEMA = 0, FORCE_HTTP = 1, FORCE_HTTPS = 2
 * @return string https | http
 */
function schema(){
    return 'https';// Bug 15679 Move to HTTPS
}

/**
 * @param integer  $schema DONT_FORCE_SCHEMA = 0, FORCE_HTTP = 1, FORCE_HTTPS = 2
 * @param string $subdomain www,next,beta,aeonflux,api,ramasaml
 * @return string http[s]://[subdomain].base_url
 */
function url($schema=FORCE_HTTPS,$subdomain=SUBDOMAIN):string{
    return 'https://' . \app_env()['u']($subdomain);
}

/**
 * @param string $subdomain www,next,aeonflux,api,ramasaml
 * @return string http[s]://[subdomain].base_url
 */
function lms($subdomain=SUBDOMAIN):string{
    return 'https://' . \app_env()['u']($subdomain);
}

/**
 * Alyasing URL()
 * 
 * @param string $schema
 * @param string $subdomain
 * @return string
 */
function home($schema=FORCE_HTTPS,$subdomain=SUBDOMAIN):string{
    return url($schema,$subdomain);
}

/**
 * return the current org's landing page url
 * 
 * @param string $schema
 * @param string $subdomain
 * @return string
 */
function home_org($schema=FORCE_HTTPS,$subdomain=SUBDOMAIN):string{
    return home($schema,$subdomain) . get_org_path();
}

/**
 * TODO decide how to handle input which does not result in an org path
 * 
 * get org path in the url 
 * 
 * @param mixed $organization_identifier
 * @return string
 */
function get_org_path($organization_identifier=null):string{
    static $org_data = [
        3   => 'mwhc',
        4   => 'mguh',
        7   => 'mharbor',
        8   => 'mfsmc',
        9   => 'mgsh',
        11  => 'mumh',
        16  => 'mh',
        21  => 'sitel',
        28  => 'msmh',
        36  => 'msmhc'
    ];
    
    if(!$organization_identifier){
        $path = \Organization_Current::path();
        if($path){
            $path = "/{$path}";
        }
        return $path;
    }
    
    if(\is_numeric($organization_identifier)){
        $org_data[$organization_identifier] = $org_data[$organization_identifier]??(new \Redis_Organization_Main($organization_identifier))->org()->get()->path;
        return "/{$org_data[$organization_identifier]}";
    }
    return "/{$organization_identifier}";//assuuming it is path
}

function content_bucket(){
    return \app_env()['paths']['lms-apps']['content.sitelms.net']['root'];
}

/**
 * Return the url for the static assets [files] for current subdomain
 * 
 * @param string $schema
 * @return string
 */
function files(){
    return www() . '/files';
}

/**
 *  
 *  @param string $schema
 */
function www(){
	return lms('www');
}

function next(){
    return lms('next');
}

function aeon(){
    return lms('aeonflux');
}

/**
 * get full org url for www
 * 
 * @param integer $schema DONT_FORCE_SCHEMA = 0, FORCE_HTTP = 1, FORCE_HTTPS = 2
 * @param mixed $organization_identifier org_id | org_path
 * @return string
 */
function www_org($schema = FORCE_HTTPS, $organization_identifier = null){
    return www() . get_org_path($organization_identifier);
}

/**
 * get full org url for next
 *
 * @param integer $schema DONT_FORCE_SCHEMA = 0, FORCE_HTTP = 1, FORCE_HTTPS = 2
 * @param mixed $organization_identifier org_id | org_path
 * @return string
 */
function next_org($schema = FORCE_HTTPS, $organization_identifier = null){
    return next() . get_org_path($organization_identifier);
}

/**
 * Return the root of the static assets cdn
 * 
 * @return string url no trailing slash
 */
function aeon_static():string{
    return \app_env()['paths']['sub']['aeonflux']['static'];
}

function aeon_img():string{
    return aeon_static() . '/img';
}

function www_static():string{
    return \app_env()['paths']['sub']['www']['static'];
}

function www_img():string{
    return www_static() . '/img';
}

function next_static():string{
    return \app_env()['paths']['sub']['next']['static'];
}

function next_img():string{
    return next_static() . '/img';
}

/**
 * cloudfront for splash pages, in whcstaticassets S3
 * @return string
 */
function splash():string{
    return 'https://dnjuo9wi5u1e9.cloudfront.net/splash';
}


