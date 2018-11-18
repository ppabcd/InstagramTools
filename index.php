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

try {
    $ig->login($username, $password);
    $ig_info       = $ig->people->getUserIdForName(getenv('TARGET'));
    $rankToken = \InstagramAPI\Signatures::generateUUID();
    $followers = [];
    $following = [];
    $max_id = null;
    $i = 0;
    do{
        $ig_followers = $ig->people->getFollowers($ig_info,$rankToken,null,$max_id);
        foreach(json_decode($ig_followers)->users as $userData){
            $followers[] = $userData->username;
        }
        $max_id = json_decode($ig_followers)->next_max_id;
        echo $i++;
    } while($max_id != null);

    $max_id = null;
    do{
        $ig_following = $ig->people->getFollowing($ig_info,$rankToken,null,$max_id);
        foreach(json_decode($ig_following)->users as $userData){
            $following[] = $userData->username;
        }
        $max_id = json_decode($ig_following)->next_max_id;
        echo $i++;
    } while($max_id != null);
    sort($following);
    sort($followers);
    $d_followers =[];
    $d_following =[];
    foreach($followers as $folls){
        if(!in_array($folls,$following)){
            $d_followers[] = $folls;
        }
    }
    foreach($following as $follw){
        if(in_array($follw,$followers)){
        
        } else {
            $d_following[] = $follw;
        }
    }
    // Num who not following you
    var_dump($d_following);
    // Num who you not follow
    // var_dump($d_followers);
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
    exit(0);
}