<?php
/**
* HeroThemes - Color Functions
* by HeroThemes (http://herothemes.com)
*/

/**
* converts a hex colour to an rgb representation
* returns (array) the RGB values for the parameter hex_colour supplied
*/
function ht_get_rgb_from_hex($hex_colour) {
   $hex_colour = str_replace("#", "", $hex_colour);

   if(strlen($hex_colour) == 6) {
      $red = hexdec(substr($hex_colour,0,2));
      $green = hexdec(substr($hex_colour,2,2));
      $blue = hexdec(substr($hex_colour,4,2));      
   } else {
      //repeat the hex value for shortcode
      $red_hex = substr($hex_colour,0,1);
      $green_hex = substr($hex_colour,1,1);
      $blue_hex = substr($hex_colour,2,1);
      $red = hexdec($red_hex.$red_hex);
      $green = hexdec($green_hex.$green_hex);
      $blue = hexdec($blue_hex.$blue_hex);
   }
   $rgb = array('red' => $red, 'green' => $green, 'blue' => $blue);
   return $rgb; 
}


function ht_colour_brightness($hex, $percent) {
    // Work out if hash given
    $hash = '';
    if (stristr($hex,'#')) {
        $hex = str_replace('#','',$hex);
        $hash = '#';
    }
    /// HEX TO RGB
    $rgb = array(hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2)));
    //// CALCULATE 
    for ($i=0; $i<3; $i++) {
        // See if brighter or darker
        if ($percent > 0) {
            // Lighter
            $rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1-$percent));
        } else {
            // Darker
            $positivePercent = $percent - ($percent*2);
            $rgb[$i] = round($rgb[$i] * $positivePercent) + round(0 * (1-$positivePercent));
        }
        // In case rounding up causes us to go to 256
        if ($rgb[$i] > 255) {
            $rgb[$i] = 255;
        }
    }
    //// RBG to Hex
    $hex = '';
    for($i=0; $i < 3; $i++) {
        // Convert the decimal digit to hex
        $hexDigit = dechex($rgb[$i]);
        // Add a leading zero if necessary
        if(strlen($hexDigit) == 1) {
        $hexDigit = "0" . $hexDigit;
        }
        // Append to the hex string
        $hex .= $hexDigit;
    }
    return $hash.$hex;
}
