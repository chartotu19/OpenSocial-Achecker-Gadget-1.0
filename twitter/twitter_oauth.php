<?php
  require("twitteroauth.php");  
session_start();
// TwitterOAuth instance, with two new parameters we got in twitter_login.php  
$twitteroauth = new TwitterOAuth('Df3Uey6cMK78sFUMnHigAw', 'qm0mVPYDuASErzgFuNawHD4nfDT170nCMfyC19btk', $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);  
// Let's request the access token  
$access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']); 
// Save it in a session var 
$_SESSION['access_token'] = $access_token; 
// Let's get the user's info 
$user_info = $twitteroauth->get('account/verify_credentials'); 
// Print user's info  
//print_r($user_info); 
$twitteroauth->post('statuses/update', array('status' => "I validated ".$_SESSION['link']." using Achecker Opensocial Gadget. Errors:".$_SESSION['errors']." Potential Problems:".$_SESSION['p_probs']." Likely Problems:".$_SESSION['l_probs']." "));
header("location:http://www.twitter.com");
//echo " You have successfully twitted the result!"; 
?>