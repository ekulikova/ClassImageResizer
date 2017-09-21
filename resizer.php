<?php
include ('ClassImageResizer.php');

 list($d,$w,$h, $skip_small)= array_slice($argv,1,4);

 echo "d,w,h = $d,$w,$h \n";
 imageResizer::resizeDir($d,$w,$h,$skip_small,'resize_save_proportion');