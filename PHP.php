/**
 * Input: hex color
 * Output: hsl(in ranges from 0-1)
 *
 * Takes the hex, converts it to RGB, and sends
 * it to RGBToHsl.  Returns the output.
 *
 */

function hexToHsl($hex) {
  $r = "";
  $g = "";
  $b = "";

  $hex = str_replace('#', '', $hex);

  if (strlen($hex) == 3) {
    $r = substr($hex, 0, 1);
    $r = $r . $r;
    $g = substr($hex, 1, 1);
    $g = $g . $g;
    $b = substr($hex, 2, 1);
    $b = $b . $b;
  } elseif (strlen($hex) == 6) {
    $r = substr($hex, 0, 2);
    $g = substr($hex, 2, 2);
    $b = substr($hex, 4, 2);
  } else {
    return false;
  }

  $r = hexdec($r);
  $g = hexdec($g);
  $b = hexdec($b);

  $hsl =  rgbToHsl($r,$g,$b);
  return $hsl;
}


/**
 *
 * Credits:
 * http://stackoverflow.com/questions/4793729/rgb-to-hsl-and-back-calculation-problems
 * http://www.niwa.nu/2013/05/math-behind-colorspace-conversions-rgb-hsl/
 *
 * Called by hexToHsl by default.
 *
 * Converts an RGB color value to HSL. Conversion formula
 * adapted from http://www.niwa.nu/2013/05/math-behind-colorspace-conversions-rgb-hsl/.
 * Assumes r, g, and b are contained in the range [0 - 255] and
 * returns h, s, and l in the format Degrees, Percent, Percent.
 *
 * @param   Number  r       The red color value
 * @param   Number  g       The green color value
 * @param   Number  b       The blue color value
 * @return  Array           The HSL representation
*/

function rgbToHsl($r, $g, $b){
  //For the calculation, rgb needs to be in the range from 0 to 1. To convert, divide by 255 (ff).
  $r /= 255;
  $g /= 255;
  $b /= 255;

  $myMax = max($r, $g, $b);
  $myMin = min($r, $g, $b);

  $maxAdd = ($myMax + $myMin);
  $maxSub = ($myMax - $myMin);

  //luminence is (max + min)/2
  $h = 0;
  $s = 0;
  $l = ($maxAdd / 2.0);

  //if all the numbers are equal, there is no saturation (greyscale).
  if($myMin != $myMax){
    if ($l < 0.5) {
      $s = ($maxSub / $maxAdd);
    } else {
      $s = (2.0 - $maxSub);
      $s = ($maxSub / $s);
    }

    //find hue
    switch($myMax){
      case $r:
      $h = ($g - $b);
      $h = ($h / $maxSub);
      break;
      case $g:
      $h = ($b - $r);
      $h = ($h / $maxSub);
      $h = ($h + 2.0);
      break;
      case $b:
      $h = ($r - $g);
      $h = ($h / $maxSub);
      $h = ($h + 4.0);
      break;
    }
  }

  $hsl = hslToDegPercPerc($h, $s, $l);
  return $hsl;
}

/**
 * Input: HSL in ranges 0-1.
 * Output: HSL in format Deg, Perc, Perc.
 *
 * Note: rgbToHsl calls this function by default.
 *
 * Multiplies $h by 60, and $s and $l by 100.
 */

function hslToDegPercPerc($h, $s, $l) {
  //convert h to degrees
  $h *= 60;

  if ($h < 0) {
    $h += 360;
  }

  //convert s and l to percentage
  $s *= 100;
  $l *= 100;

  $hsl['h'] = $h;
  $hsl['s'] = $s;
  $hsl['l'] = $l;
  return $hsl;
}

/**
 * Input: HSL in format Deg, Perc, Perc
 * Output: An array containing HSL in ranges 0-1
 *
 * Divides $h by 60, and $s and $l by 100.
 *
 * hslToRgb calls this by default.
 */

function degPercPercToHsl($h, $s, $l) {
  //convert h, s, and l back to the 0-1 range

  //convert the hue's 360 degrees in a circle to 1
  $h /= 360;

  //convert the saturation and lightness to the 0-1
  //range by multiplying by 100
  $s /= 100;
  $l /= 100;

  $hsl['h'] =  $h;
  $hsl['s'] = $s;
  $hsl['l'] = $l;

  return $hsl;
}

/**
 * Converts an HSL color value to RGB. Conversion formula
 * adapted from http://www.niwa.nu/2013/05/math-behind-colorspace-conversions-rgb-hsl/.
 * Assumes h, s, and l are in the format Degrees,
 * Percent, Percent, and returns r, g, and b in
 * the range [0 - 255].
 *
 * Called by hslToHex by default.
 *
 * Calls:
 *   degPercPercToHsl
 *   hueToRgb
 *
 * @param   Number  h       The hue value
 * @param   Number  s       The saturation level
 * @param   Number  l       The luminence
 * @return  Array           The RGB representation
 */

function hslToRgb($h, $s, $l){
  $hsl = degPercPercToHsl($h, $s, $l);
  $h = $hsl['h'];
  $s = $hsl['s'];
  $l = $hsl['l'];

  //If there's no saturation, the color is a greyscale,
  //so all three RGB values can be set to the lightness.
  //(Hue doesn't matter, because it's grey, not color)
  if ($s == 0) {
    $r = $l * 255;
    $g = $l * 255;
    $b = $l * 255;
  }
  else {
    //calculate some temperary variables to make the
    //calculation eaisier.
    if ($l < 0.5) {
      $temp2 = $l * (1 + $s);
    } else {
      $temp2 = ($l + $s) - ($s * $l);
    }
    $temp1 = 2 * $l - $temp2;

    //run the calculated vars through hueToRgb to
    //calculate the RGB value.  Note that for the Red
    //value, we add a third (120 degrees), to adjust
    //the hue to the correct section of the circle for
    //red.  Simalarly, for blue, we subtract 1/3.
    $r = 255 * hueToRgb($temp1, $temp2, $h + (1 / 3));
    $g = 255 * hueToRgb($temp1, $temp2, $h);
    $b = 255 * hueToRgb($temp1, $temp2, $h - (1 / 3));
  }

  $rgb['r'] = $r;
  $rgb['g'] = $g;
  $rgb['b'] = $b;

  return $rgb;
}

/**
 * Converts an HSL hue to it's RGB value.
 *
 * Input: $temp1 and $temp2 - temperary vars based on
 * whether the lumanence is less than 0.5, and
 * calculated using the saturation and luminence
 * values.
 *  $hue - the hue (to be converted to an RGB
 * value)  For red, add 1/3 to the hue, green
 * leave it alone, and blue you subtract 1/3
 * from the hue.
 *
 * Output: One RGB value.
 *
 * Thanks to Easy RGB for this function (Hue_2_RGB).
 * http://www.easyrgb.com/index.php?X=MATH&$h=19#text19
 *
 */
function hueToRgb($temp1, $temp2, $hue) {
  if ($hue < 0) {
    $hue += 1;
  }
  if ($hue > 1) {
    $hue -= 1;
  }

  if ((6 * $hue) < 1 ) {
    return ($temp1 + ($temp2 - $temp1) * 6 * $hue);
  } elseif ((2 * $hue) < 1 ) {
    return $temp2;
  } elseif ((3 * $hue) < 2 ) {
    return ($temp1 + ($temp2 - $temp1) * ((2 / 3) - $hue) * 6);
  }
  return $temp1;
}


/**
 * Converts HSL to Hex by converting it to
 * RGB, then converting that to hex.
 *
 * string hslToHex($h, $s, $l[, $prependPound = true]
 *
 * $h is the Degrees value of the Hue
 * $s is the Percentage value of the Saturation
 * $l is the Percentage value of the Lightness
 * $prependPound is a bool, whether you want a pound
 *  sign prepended. (optional - default=true)
 *
 * Calls:
 *   hslToRgb
 *
 * Output: Hex in the format: #00ff88 (with
 * pound sign).  Rounded to the nearest whole
 * number.
 */

function hslToHex($h, $s, $l, $prependPound = true) {
  //convert hsl to rgb
  $rgb = hslToRgb($h,$s,$l);

  //convert rgb to hex
  $hexR = $rgb['r'];
  $hexG = $rgb['g'];
  $hexB = $rgb['b'];

  //round to the nearest whole number
  $hexR = round($hexR);
  $hexG = round($hexG);
  $hexB = round($hexB);

  //convert to hex
  $hexR = dechex($hexR);
  $hexG = dechex($hexG);
  $hexB = dechex($hexB);

  //check for a non-two string length
  //if it's 1, we can just prepend a
  //0, but if it is anything else non-2,
  //it must return false, as we don't
  //know what format it is in.
  if (strlen($hexR) != 2) {
    if (strlen($hexR) == 1) {
      //probably in format #0f4, etc.
      $hexR = "0" . $hexR;
    } else {
      //unknown format
      return false;
    }
  }
  if (strlen($hexG) != 2) {
    if (strlen($hexG) == 1) {
      $hexG = "0" . $hexG;
    } else {
      return false;
    }
  }
  if (strlen($hexB) != 2) {
    if (strlen($hexB) == 1) {
      $hexB = "0" . $hexB;
    } else {
      return false;
    }
  }

  //if prependPound is set, will prepend a
  //# sign to the beginning of the hex code.
  //(default = true)
  $hex = "";
  if ($prependPound) {
    $hex = "#";
  }

  $hex = $hex . $hexR . $hexG . $hexB;

  return $hex;
}
