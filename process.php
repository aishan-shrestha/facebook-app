<?php
date_default_timezone_set('UTC');
session_start();
require_once __DIR__ . '/vendor/autoload.php'; //include facebook api library

$config = require_once 'config.php';


if( isset($_GET["pid"]) ) {
	$_SESSION["pic_id"] = $_GET["pid"]; // Picture ID from Index page
}


switch( $_SESSION["pic_id"] ) {
    case 1:
        $PicLocation = 'cover_pics/cover1.jpg';
        break;
    case 2:
        $PicLocation = 'cover_pics/cover2.jpg';
        break;
    case 3:
        $PicLocation = 'cover_pics/cover3.jpg';
        break;
    case 4:
        $PicLocation = 'cover_pics/cover4.jpg';
        break;
    case 5:
        $PicLocation = 'cover_pics/cover5.jpg';
        break;
    case 6:
        $PicLocation = 'cover_pics/cover6.jpg';
        break;
    case 7:
        $PicLocation = 'cover_pics/cover7.jpg';
        break;
    case 8:
        $PicLocation = 'cover_pics/cover8.jpg';
        break;
    case 9:
        $PicLocation = 'cover_pics/cover9.jpg';
        break;
    case 10:
        $PicLocation = 'cover_pics/cover10.jpg';
        break;
    case 11:
        $PicLocation = 'cover_pics/cover11.jpg';
        break;
    default:
       // header('Location: ' . $homeurl);
        break;
}


 $fb = new Facebook\Facebook( array(
                                  'app_id'                => $config['appId'],
				  'app_secret'            => $config['appSecret'],
				  'default_graph_version' => 'v2.4'
                                ));


//try to get access token
try{
	$helper = $fb->getRedirectLoginHelper();
	$session = $helper->getAccessToken();
}catch(FacebookRequestException $ex){
	die(" Facebook Message: " . $ex->getMessage());
} catch(Exception $ex){
	die( " Message: " . $ex->getMessage());
}

//get picture ready for upload
$data = array('message' => '','source' => $fb->fileToUpload($PicLocation) );


//try upload photo to facebook wall
if( $session ) {
	try{
		$photo_response = $fb->post('/me/photos', $data, $session);
		$graph_node = $photo_response->getGraphNode();
	} catch(FacebookRequestException $ex){
		die(" Facebook Message: " . $ex->getMessage());
	} catch(Exception $ex){
        die( " Message: " . $ex->getMessage());
	}
} else{
	//if login requires redirect user to facebook login page
	$login_url = $helper->getLoginUrl($config['return_url'], array('scope'=> $config['fbPermissions']));
	header('Location: '. $login_url);
	exit();
}


if( isset($graph_node["id"]) && is_numeric($graph_node["id"]) ) {
    /*
    image is posted in user facebook account, but still we need to send user to facebook
    so s/he can set cover or profile picture!
    */

    //Get url of the picture just uploaded in user facebook account
    $jsonurl = "https://graph.facebook.com/".$graph_node["id"]."?access_token=".$session;
    $json = file_get_contents($jsonurl,0,null,null);
    $json_output = json_decode($json);

    /*
    We can not set facebook cover or profile picture automatically,
    So the trick is to post picture into user facebook account first
    and then redirect them to a facebook profile page where they just have to click a button to set it.
    */
    echo '<html><head><title>Update Image</title>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo '<link href="style.css" rel="stylesheet" type="text/css" />';
    echo '</head><body>';
    echo '<div align="center" class="fbpicwrapper">';
    echo '<h1>Image is sent to your facebook account!</h1>';
    echo '<div class="fbpic_desc">Click on desired button you want to do with this image!</div>';
    echo '<div class="option_img"><img src="'.$json_output->source.'" /></div>';

    /*
    Links (buttons) below will send user to facebook page,
    where they just need to crop or correct propertion of image and hit apply button.
    */
    echo '<a class="button" target="_blank" href="http://www.facebook.com/profile.php?preview_cover='.$graph_node["id"].'">Make Your Profile Cover</a>';
    echo '<a class="button" target="_blank" href="http://www.facebook.com/photo.php?fbid='.$graph_node["id"].'&type=1&makeprofile=1&makeuserprofile=1">Make Your Profile Picture</a>';
    echo '<a class="button" href="'.$homeurl.'">Back to main Page.</a>';
    echo '</div>';
    echo '</body></html>';
}


if ( isset($graph_node["id"]) && is_numeric($graph_node["id"]) && isset( $_POST['upload-cover-image-facebook'] ) ) {
    $jsonurl = "https://graph.facebook.com/".$graph_node["id"]."?access_token=".$session;
    $json = file_get_contents($jsonurl,0,null,null);
    $json_output = json_decode($json);

    echo '<script>location.href="http://www.facebook.com/profile.php?preview_cover='.$graph_node["id"].'";</script>';
    exit();

} else if( isset($graph_node["id"]) && is_numeric($graph_node["id"]) && isset( $_POST['upload-profile-image-facebook'] ) ) {
    $jsonurl = "https://graph.facebook.com/".$graph_node["id"]."?access_token=".$session;
    $json = file_get_contents($jsonurl,0,null,null);
    $json_output = json_decode($json);
    echo '<script>location.href="http://www.facebook.com/photo.php?fbid='.$graph_node["id"].'&type=1&makeprofile=1&makeuserprofile=1";</script>';
    exit();

}

