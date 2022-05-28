<?php
/**
 * TODO TO BE REPLACED WITH A WORKING RECAPTCHA !!
 *
 * Form element for Recaptcha
 * @author Holly/Modified for Recaptcha by Preston
 */
//fatal('TOBEDELETED202104 recaptcha');
class ViewSnipets_FormElement_Recaptcha {
    
    /**
     * Render Google Recaptcha
     *
     */
    public static function render() {
        return static::loadHTML();
    }
    
    /**
     * Layout needed for recapcha (JS)
     */
    public static function loadLayout() {
        PublicFormsL::inject(PublicFormsL::LAST_HEAD,"<script src='https://www.google.com/recaptcha/api.js'></script>");
    }
    
    
    /**
     * Load html
     *
     * Following parameters needed:
     * input_placeholder
     *
     * @return string $html
     */
    public static function loadHTML() {
        warning('TOBEDELETED202045 aa6');
        $html = '';
        /** FS16415
        $sitekey    = app_env()['external sources']['GoogleRecaptcha']['secret'];
        $html =
        <<<HTML
Please check the box below.
<div class="g-recaptcha" data-sitekey="{$sitekey}"></div>
<br />
HTML;
        **/
        return $html;
    }
}
