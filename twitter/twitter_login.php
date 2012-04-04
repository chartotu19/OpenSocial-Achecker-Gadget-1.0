<?php
session_start();
$_SESSION['link']=$_GET['url'];
$data=explode("^",$_GET['total']);
$_SESSION['errors']=$data[1];
$_SESSION['l_probs']=$data[2];
$_SESSION['p_probs']=$data[3];
require("twitteroauth.php"); 
if(!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])){  
    // We've got everything we need  
    echo "ds";
} else {  
    // Something's missing, go back to square 1  
   header('Location: twitter_login.php');  
}  

// The TwitterOAuth instance  
$twitteroauth = new TwitterOAuth('Df3Uey6cMK78sFUMnHigAw', 'qm0mVPYDuASErzgFuNawHD4nfDT170nCMfyC19btk');  
// Requesting authentication tokens, the parameter is the URL we will be redirected to  
$request_token = $twitteroauth->getRequestToken('http://www.energisenow.elementfx.com/test/twitter/twitter_oauth.php');  
  
// Saving them into the session  
$_SESSION['oauth_token'] = $request_token['oauth_token'];  
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];  
  
// If everything goes well..  
if($twitteroauth->http_code==200){  
    // Let's generate the URL and redirect  
    $url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']); 
    header('Location: '. $url); 
} else { 
    // It's a bad idea to kill the script, but we've got to know when there's an error.  
    die('Something wrong happened.');  
}  