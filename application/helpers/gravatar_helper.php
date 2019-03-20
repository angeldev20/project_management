<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Gravatar Helper
 */
function get_gravatar( $email, $s = 40, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {

    $url = '//www.gravatar.com/avatar/';

    $url .= md5( strtolower( trim( $email ) ) );

    $url .= "?s=$s&d=$d&r=$r";

    if ( !$url ) {
      $baseUrl = 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION['accountUrlPrefix'] . '/';
      $url = $baseUrl . "files/media/no-pic.png";
    }

    return $url;

}
function get_user_pic($pic = FALSE, $email = FALSE, $pixel = FALSE){
    if($pic != 'no-pic.png')
    {
        $baseUrl = 'https://spera-' . ENVIRONMENT . '.s3-us-west-2.amazonaws.com/' . $_SESSION['accountUrlPrefix'] . '/';
        $image = $baseUrl . "files/media/".$pic;
            if($pixel)
            {
                $pic_in_pixel = base_url()."files/media/".$pixel."_".$pic;
                if(!file_exists($pic_in_pixel)){

                }
                return $pic_in_pixel;
            }else{
                return $image;
            }

    }
    else
    {
    	    return false;
            //return get_gravatar($email);
    }

}