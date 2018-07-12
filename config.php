<?php
return [
    
    'appId'         => '<your_app_id>', //Facebook App ID
    'appSecret'     => '<your_app_secret>', //Facebook App Secret
    'return_url'    => "<path_to_your_domain>/facebook-app/process.php", //return url (url to script)
    'homeurl'       => "<path_to_your_domain>/facebook-app/", //return to home
    'fbPermissions' => 'publish_actions, user_photos';  //Required facebook permissions
   
];
