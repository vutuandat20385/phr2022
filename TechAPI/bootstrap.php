<?php
require_once realpath(__DIR__) . '/Autoload.php';

TechAPIAutoloader::register();

use TechAPI\Constant;
use TechAPI\Client;
use TechAPI\Auth\ClientCredentials;

// config api
Constant::configs(array(
    'mode'            => Constant::MODE_LIVE,
    // 'mode'              => Constant::MODE_SANDBOX,
    'connect_timeout'   => 15,
    'enable_cache'      => true,
    'enable_log'        => true,
    'log_path'          => realpath(__DIR__) . '/logs'
));


// config client and authorization grant type
function getTechAuthorization()
{    
    $client = new Client(
        //'YOUR_CLIENT_ID',
        //'YOUR_CLIENT_SECRET',
        //'e615D85fc918f252e1754Ce2391c8Ef923AAB401',
        //'663642d023602e28784F8789dC939f14a54ece5f588848beBdd6314fab8c274de8B618a4',
        '6250df42a42e8f0b75d35de54277c0580e64BA9d',
        'd5F41c3d16daf2a35fc9fd33acFfb5f9525604f4dee9e56ef5c78bB621fAc3FbdeCc94d2',
        // array('send_mt_active') 
        array('send_brandname_otp')
    );
    
    return new ClientCredentials($client);
}