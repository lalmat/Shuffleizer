<?php
require("shuffleizer.class.php");

$action = $argv[1];
$file   = $argv[2];
$paswd  = $argv[3];

switch($action) {
  case "encrypt":
    shuffleizer::encrypt($file);
    break;
  case "uncrypt":
    shuffleizer::decrypt($file, $paswd);
    break;
  default:
    showHelp();
}

function showHelp() {
  echo "
Image Shuffleizer : (Mathieu LALLEMAND - @lalmat) 
-------------------------------------------------

$ shuffleizer [action] [file] [password]

Ex: $ shuffleizer encrypt myFile.jpg SecretPassword

";
}