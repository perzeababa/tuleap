<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
function autoloadc52f8000524ab0e9492089adea268e48($class) {
    static $classes = null;
    if ($classes === null) {
        $classes = array(
            'openid_accountmanager' => '/AccountManager.class.php',
            'openid_authenticationfailedexception' => '/AuthenticationFailedException.class.php',
            'openid_connexionmanager' => '/ConnexionManager.class.php',
            'openid_dao' => '/Dao.class.php',
            'openid_driver_connexiondriver' => '/driver/ConnexionDriver.class.php',
            'openid_identityurlalreadypairedexception' => '/IdentityUrlAlreadyPairedException.class.php',
            'openid_identityurlupdateexception' => '/IdentityUrlUpdateException.class.php',
            'openid_logincontroller' => '/LoginController.class.php',
            'openid_openidexception' => '/OpenIdException.class.php',
            'openid_openidrouter' => '/OpenIdRouter.class.php',
            'openid_usernotfoundexception' => '/UserNotFoundException.class.php',
            'openidplugin' => '/openidPlugin.class.php',
            'openidplugindescriptor' => '/OpenidPluginDescriptor.class.php',
            'openidplugininfo' => '/OpenidPluginInfo.class.php'
        );
    }
    $cn = strtolower($class);
    if (isset($classes[$cn])) {
        require dirname(__FILE__) . $classes[$cn];
    }
}
spl_autoload_register('autoloadc52f8000524ab0e9492089adea268e48');
// @codeCoverageIgnoreEnd