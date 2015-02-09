<?php
/**
 * Created by PhpStorm.
 * User: luiscunha
 * Date: 7/27/14
 * Time: 12:28 PM
 */


$config['cookies'] = array(
    'expires'       => '20 minutes',
    'path'          => '/',
    'domain'        => null,
    'secure'        => false,
    'httponly'      => false,
    'name'          => 'slim_session',
    'secret'        => 'MWqUDk8jUJHfFv04hTwbuzL0',
    'cipher'        => MCRYPT_RIJNDAEL_256,
    'cipher_mode'   => MCRYPT_MODE_CBC
);