<?php
require __DIR__.'/vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

/////// CONFIG ///////
$username = getenv("IG_USERNAME");
$password = getenv('IG_PASS');
$debug = true;
$truncatedDebug = false;
//////////////////////

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

try{
    // Login
    $ig->login($username, $password);

    $file = json_decode(file_get_contents("output.json"));
    foreach($file as $user){
        $userId = $ig->people->getUserIdForName($user);
        
        $ig->people->unfollow($userId);
        sleep(5);
    }
    
} catch(\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
    exit(0);
}