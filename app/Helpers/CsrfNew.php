<?php

namespace Helpers;

use Helpers\Sesiones;
/**
 * Cross Site Request Forgery helper
 *
 * @author Diego Ossa
 * @date Oct 01 2015
 */

class CsrfNew {

    /**
     * get CSRF token and generate a new one if expired
     *
     * @access public
     * @static static method
     * @return string
     */
    public static function makeToken()
    {
        $max_time    = CSRF_LIFE; // token is valid for 1 day
        $stored_time = Sesiones::getCsrfToken();
        $csrf_token  = Sesiones::getCsrfTokenTime();

        if($max_time + $stored_time <= time() || empty($csrf_token))
            Sesiones::setCsrf(md5(uniqid(rand(), true)), time());

        return Sesiones::getCsrfToken();
    }
    
    /**
     * checks if CSRF token in session is same as in the form submitted
     *
     * @access public
     * @static static method
     * @return bool
     */
    public static function isTokenValid(){
        return $_POST['csrf_token'] === Sesiones::getCsrfToken();
    }

}
