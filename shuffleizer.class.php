<?php
class shuffleizer {

  public static function encrypt($file) {

    // Load the image
    $imInfo = getimagesize($file);
    $im = static::loadImage($file, $imInfo['mime']);

    // Randomize 3 colors components
    $alt['r'] = rand(0,255);
    $alt['g'] = rand(0,255);
    $alt['b'] = rand(0,255);

    // Change the colors of the image
    echo "Creating image : ".$imInfo[0]."x".$imInfo[1].PHP_EOL;
    $im2 = imageCreateTrueColor($imInfo[0], $imInfo[1]);

    for ($x=0; $x<$imInfo[0]; $x++) {
      for ($y=0; $y<$imInfo[1]; $y++) {
        $pixel = static::getRGB($im, $x, $y);
        $pixelAlt['r'] = ($pixel['r']+$alt['r'])%256;
        $pixelAlt['g'] = ($pixel['g']+$alt['g'])%256;
        $pixelAlt['b'] = ($pixel['b']+$alt['b'])%256;
        // Is it a known color ?
        // $colorExists = imagecolorexact($im2, $pixelAlt['r'], $pixelAlt['g'], $pixelAlt['b']);
        // $color = ($colorExists > 0) ? $colorExists : imagecolorallocate($im2, $pixelAlt['r'], $pixelAlt['g'], $pixelAlt['b']);
        $rgb = imageColorAllocate($im2, $pixelAlt['r'], $pixelAlt['g'], $pixelAlt['b']);
        imageSetPixel($im2, $x, $y, $rgb);
      }
    }

    // Old image is useless now...
    imageDestroy($im);

    // Split in 4x4 matrix
    $matrix = 5;
    $imSuff = array();
    $imPict = array();
    $sizeChuckX = floor($imInfo[0] / $matrix);
    $sizeChuckY = floor($imInfo[1] / $matrix);

    // Shuffle the array
    for ($i=0; $i<$matrix; $i++) {
      for ($j=0; $j<$matrix; $j++) {
        $imSuff[] = array('i'=>($i."x".$j), 'x'=>$i, 'y'=>$j);
      }
    }
    shuffle($imSuff);

    for ($i=0; $i<$matrix; $i++) {
      for ($j=0; $j<$matrix; $j++) {
        $imPict[$i][$j] = imageCreate($sizeChuckX, $sizeChuckY);
        imageCopy($imPict[$i][$j], $im2, 0, 0, $sizeChuckX*$i, $sizeChuckY*$j, $sizeChuckX, $sizeChuckY);
      }
    }
    imageDestroy($im2);

    $sImg = imageCreate($imInfo[0], $imInfo[1]);
    $aryCount = 0;
    for ($i=0; $i<$matrix; $i++) {
      for ($j=0; $j<$matrix; $j++) {
        $xPos = $imSuff[$aryCount]['x'];
        $yPos = $imSuff[$aryCount]['y'];
        $aryCount++;

        imageCopy($sImg, $imPict[$xPos][$yPos], $i*$sizeChuckX, $j*$sizeChuckY, 0, 0, $sizeChuckX, $sizeChuckY);
      }
    }

    // Save the suffled image
    static::saveImage($sImg, "shuffle.jpg", $imInfo['mime']);
    unset($sImg);

    // Save the suffle
    $shuffle['color'] = $alt;
    $shuffle['rand'] = $imSuff;
    file_put_contents("shuffle.dat", gzencode(json_encode($shuffle),9));
  }

  public static function decrypt($imgfile, $datafile) {

  }


  private static function getRGB($im, $x, $y) {
    $rgb = imagecolorat($im, $x, $y);
    $result['r'] = ($rgb >> 16) & 0xFF;
    $result['g'] = ($rgb >> 8) & 0xFF;
    $result['b'] = $rgb & 0xFF;
    return $result;
  }

  private static function loadImage($image, $mime) {
    switch($mime) {
      case "image/jpeg":
        return imageCreateFromJpeg($image);
        break;

      case "image/gif":
        return imageCreateFromJpeg($image);
        break;

      case "image/png":
        return imageCreateFromJpeg($image);
        break;

      throw new Exception("Format d'image invalide (JPG/PNG/GIF uniquement)");
    }
  }

  private static function saveImage($image, $filename, $mime) {
    switch($mime) {
      case "image/jpeg":
        return imageJpeg($image, $filename);
        break;

      case "image/gif":
        return imageGif($image, $filename);
        break;

      case "image/png":
        return imagePng($image, $filename, 85);
        break;

      throw new Exception("Format d'image invalide (JPG/PNG/GIF uniquement)");
    }
  }
}