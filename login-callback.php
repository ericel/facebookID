<?php
session_start(); 
require_once __DIR__ . '/vendor/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => '404119723313406', // Replace {app-id} with your app id
  'app_secret' => 'fe6bd916ddbe78d64f3f35633fb3d977',
  'default_graph_version' => 'v2.2',
  ]);

$helper = $fb->getRedirectLoginHelper();
 
try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}
 
if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
  exit;
}
 
// Logged in
 if ( isset( $accessToken  ) ) { 
// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();
 
// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
 
// Get user’s Facebook ID
$userId = $tokenMetadata->getField('user_id');

try {
  // Returns a `Facebook\FacebookResponse` object
  $response = $fb->get('/me?fields=id,name,gender', $accessToken);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$user = $response->getGraphUser();
 
$userId = $user['id']; // Retrieve user Id
$userName = $user['name']; // Retrieve user name
$gender = $user['gender']; // Retrieve user name
$home_town = $user['user_location']['name'];
/* ---- Session Variables -----*/
$_SESSION['FBID'] = $userId;
$_SESSION['FULLNAME'] = $userName;
$_SESSION['gender'] = $gender;
$_SESSION['hometown'] = $home_town;

header("Location: index.php");
} else {
	header("Location: login.php");
}
?>