<?php
session_start(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($_SESSION['FBID']){
echo $_SESSION['gender'];
$temp_folder        = "tmp/"; 
$fb_image = "https://graph.facebook.com/".$_SESSION['FBID']."/picture"; 
$image_id_png       = 'assets/id.png'; // id card image template path
$font               = 'assets/fonts/DidactGothic.ttf'; //font used

//copy user profile image from facebook in temp folder
  echo $_SERVER["DOCUMENT_ROOT"]."/westcameroon/tmp/"; 

    //copy user profile image from facebook in temp folder
    if(!copy('http://graph.facebook.com/'.$_SESSION['FBID'].'/picture?width=100&height=100',$temp_folder.$_SESSION['FBID'].'.jpg'))
    {
        die('Could not copy image!');
    }

   ##### start generating Facebook ID ########
    $dest = imagecreatefrompng($image_id_png); // source id card image template
    $src = imagecreatefromjpeg($temp_folder.$_SESSION['FBID'].'.jpg'); //facebook user image stored in our temp folder
    
    imagealphablending($dest, false); 
    imagesavealpha($dest, true);
    
    //merge user picture with id card image template
    //need to play with numbers here to get alignment right
    imagecopymerge($dest, $src, 320, 32, 0, 0, 100, 100, 100); 
    
    //colors we use for font
    $facebook_blue = imagecolorallocate($dest, 81, 103, 147); // Create blue color
    $facebook_grey = imagecolorallocate($dest, 74, 74, 74); // Create grey color
    
    //Texts to embed into id card image template
    $txt_user_id        = $_SESSION['FBID'];
    $txt_user_name      = isset($_SESSION['FULLNAME'])?$_SESSION['FULLNAME']:'No Name';
    $txt_user_gender    = isset($_SESSION['gender'])?$_SESSION['gender']:'No gender';
    $txt_user_hometown  = isset($_SESSION['hometown'])?$_SESSION['hometown']:'Unknown';
    $txt_user_birth     = isset($user_profile['birthday'])?$user_profile['birthday']:'00/00/0000';
    $user_text          = 'Your source for Google+ and hangout graphics for free.';
    $txt_credit         = 'Generated using www.saaraan.com';
    
    //format birthday, not showing whole birth date!
    $fb_birthdate = date($txt_user_birth);
    $sort_birthdate = strtotime($fb_birthdate);
    $for_birthdate = date('d M', $sort_birthdate);

    imagealphablending($dest, true); //bring back alpha blending for transperent font
    
    imagettftext($dest, 10, 0, 170, 190, $facebook_grey , $font, $txt_user_id); //Write user id to id card
    imagettftext($dest, 15, 0, 25, 105, $facebook_grey, $font, $txt_user_name); //Write name to id card
    imagettftext($dest, 15, 0, 25, 147, $facebook_grey, $font, $txt_user_gender); //Write gender to id card
    imagettftext($dest, 15, 0, 170, 147, $facebook_grey, $font, $txt_user_hometown); //Write hometown to id card
    imagettftext($dest, 15, 0, 25, 190, $facebook_grey, $font, $for_birthdate); //Write birthday to id card
    imagettftext($dest, 10, 0, 25, 215, $facebook_grey, $font, $user_text); //Write custom text to id card
    imagettftext($dest, 8, 0, 25, 240, $facebook_blue, $font, $txt_credit); //Write credit link to id card
        
    imagepng($dest, $temp_folder.'id_'.$_SESSION['FBID'].'.jpg'); //save id card in temp folder

	//now we have generated ID card, we can display or post it on facebook
    echo '<img src="tmp/id_'.$_SESSION['FBID'].'.jpg" >'; //display saved user id card
    
    /* or output image to browser directly
    header('Content-Type: image/png');
    imagepng($dest);
    */
    
	/*  //Post ID card to User Wall
        $post_url = '/'.$fbuser.'/photos';
		
        //posts message on page statues
        $msg_body = array(
        'source'=>'@'.'tmp/id_'.$fbuser.'.jpg',
        'message' => 'interesting ID';
        );
		$postResult = $facebook->api($post_url, 'post', $msg_body );
	*/
	
    imagedestroy($dest);
    imagedestroy($src);
}
?>
<img src="https://graph.facebook.com/<?php 
                     echo $_SESSION['FBID']; ?>/picture">