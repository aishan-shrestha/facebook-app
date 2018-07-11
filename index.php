<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Change Facebook Cover or Profile Pic with PHP Demo</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js"></script>

<script type="text/javascript">
//Fade in/out effect for cover images using jquery
$(document).ready(function() {
	coverpics = $('.fbcovers img');
  	$(coverpics).fadeTo("fast", 0.70);
	coverpics.mouseenter(OnEnter).mouseleave(OnLeave);
    function OnEnter(){$(this).fadeTo("fast", 1);}
    function OnLeave(){$(this).fadeTo("fast", 0.70);}
});
</script>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<?php

date_default_timezone_set('UTC');
session_start();



require_once('Facebook/autoload.php' );//include facebook api library

######### edit details ##########
$appId = '<your_app_id>'; //Facebook App ID
$appSecret = '<your_app_secret'; // Facebook App Secret
$return_url = '<path_to_your_domain>/facebook-app/process.php';  //return url (url to script)
$homeurl = '<path_to_your_domain>/facebook-app/';  //return to home
$fbPermissions = 'publish_actions, user_photos';  //Required facebook permissions
##################################
// session_destroy();

if( isset($_GET["pid"]) ) {
    $_SESSION["pic_id"] = $_GET["pid"]; // Picture ID from Index page
}

if( isset($_SESSION["pic_id"]) ) {
    // echo $_SESSION["pic_id"].'EEEE';
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
                                  'app_id' => $appId,
                                  'app_secret' => $appSecret,
                                  'default_graph_version' => 'v2.4'
                                ));


    //try to get access token
    try{
        echo 'inside try';
        $helper = $fb->getRedirectLoginHelper();
        $session = $helper->getAccessToken();
    }catch(FacebookRequestException $ex){
         echo 'inside catach';
        die(" Facebook Message: " . $ex->getMessage());
    } catch(Exception $ex){
        echo 'inside catachhhh';
        die( " Message: " . $ex->getMessage());
    }

    //get picture ready for upload
    $data = array('message' => '','source' => $fb->fileToUpload($PicLocation) );

    // print_r($session);
    //try upload photo to facebook wall
    if( $session ) {
        // echo 'in $session';
        // print_r($session);
        try{
            $photo_response = $fb->post('/me/photos', $data, $session);
            $graph_node = $photo_response->getGraphNode();
        } catch(FacebookRequestException $ex){
            die(" Facebook Message: " . $ex->getMessage());
        } catch(Exception $ex){
            die( " Message: " . $ex->getMessage());
        }
    } else{
        echo $_SESSION["pic_id"].'redirect';
        //if login requires redirect user to facebook login page
        $login_url = $helper->getLoginUrl($return_url, array('scope'=> $fbPermissions));
        // echo $login_url;
        // header('Location: '. $login_url);
        echo '<script>jQuery("a.reqlogin").attr("href", "'.$login_url. '");</script>';
        // echo '<script>window.open("'.$login_url.'", "_blank")</script>';
        echo '<script>jQuery("a.reqlogin").trigger("click");</script>';

        // exit();
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

        $jsonurl = "https://graph.facebook.com/".$graph_node["id"]."?access_token=".$session;
        $json = file_get_contents($jsonurl,0,null,null);
        $json_output = json_decode($json);

        echo '<script>window.open("http://www.facebook.com/profile.php?preview_cover='.$graph_node["id"].'","_blank")</script>';
        // exit();

    }


    /*if ( isset($graph_node["id"]) && is_numeric($graph_node["id"]) && isset( $_POST['upload-cover-image-facebook'] ) ) {


    } else if( isset($graph_node["id"]) && is_numeric($graph_node["id"]) && isset( $_POST['upload-profile-image-facebook'] ) ) {
        $jsonurl = "https://graph.facebook.com/".$graph_node["id"]."?access_token=".$session;
        $json = file_get_contents($jsonurl,0,null,null);
        $json_output = json_decode($json);
        echo '<script>location.href="http://www.facebook.com/photo.php?fbid='.$graph_node["id"].'&type=1&makeprofile=1&makeuserprofile=1";</script>';
        exit();

    }*/
}

 ?>
<body>
<div align="center" class="fbpicwrapper">
<h1>Change Facebook Cover or Profile Pic with PHP</h1>
<div class="fbpic_desc">Please click on cover image you like the most for your profile!</div>
<ul class="fbcovers">
	<li><a href="?pid=1"><img src="cover_pics/cover1.jpg" width="850" height="315" border="0" /></a></li>
    <li><a href="?pid=2"><img src="cover_pics/cover2.jpg" width="850" height="315" border="0" /></a></li>
    <li><a href="?pid=3"><img src="cover_pics/cover3.jpg" width="850" height="315" border="0" /></a></li>
    <li><a href="?pid=4"><img src="cover_pics/cover4.jpg" width="850" height="315" border="0" /></a></li>
    <li><a href="?pid=5"><img src="cover_pics/cover5.jpg" width="850" height="315" border="0" /></a></li>
    <li><a href="?pid=6"><img src="cover_pics/cover6.jpg" width="850" height="315" border="0" /></a></li>
    <li><a href="?pid=7"><img src="cover_pics/cover7.jpg" width="850" height="315" border="0" /></a></li>
    <li><a href="?pid=8"><img src="cover_pics/cover8.jpg" width="850" height="315" border="0" /></a></li>
    <li><a href="?pid=9"><img src="cover_pics/cover9.jpg" width="850" height="315" border="0" /></a></li>
    <li><a href="?pid=10"><img src="cover_pics/cover10.jpg" width="850" height="315" border="0" /></a></li>
    <li><a href="?pid=11"><img src="cover_pics/cover11.jpg" width="850" height="315" border="0" /></a></li>
</ul>
<a style="display:none" class="reqlogin" href="" target="_blank">
</div>

</body>
</html>
