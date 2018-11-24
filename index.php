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
    function indent($json) {

        $result      = '';
        $pos         = 0;
        $strLen      = strlen($json);
        $indentStr   = '  ';
        $newLine     = "\n";
        $prevChar    = '';
        $outOfQuotes = true;
    
        for ($i=0; $i<=$strLen; $i++) {
    
            // Grab the next character in the string.
            $char = substr($json, $i, 1);
    
            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;
    
            // If this character is the end of an element,
            // output a new line and indent the next line.
            } else if(($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos --;
                for ($j=0; $j<$pos; $j++) {
                    $result .= $indentStr;
                }
            }
    
            // Add the character to the result string.
            $result .= $char;
    
            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos ++;
                }
    
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }
    
            $prevChar = $char;
        }
    
        return $result;
    }

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
        $max_id = (isset(json_decode($ig_followers)->next_max_id))?json_decode($ig_followers)->next_max_id:null;
    } while($max_id != null);

    $max_id = null;
    do{
        $ig_following = $ig->people->getFollowing($ig_info,$rankToken,null,$max_id);
        foreach(json_decode($ig_following)->users as $userData){
            $following[] = $userData->username;
        }
        $max_id = (isset(json_decode($ig_following)->next_max_id))?json_decode($ig_following)->next_max_id:null;
        $i++;
    } while($max_id != null);
    sort($following);
    sort($followers);
    file_put_contents("allFollowing.json",indent(json_encode($following)));
    file_put_contents("allFollowers.json",indent(json_encode($followers)));
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
    // Output  who not following you
    file_put_contents("following.json",indent(json_encode($d_following)));
    // Output who not you follback
    file_put_contents("followers.json",indent(json_encode($d_followers)));

    echo "Success. Please check allFollowing.json, allFollowers.json, followers.json and following.json\n";
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
    exit(0);
}