<?php

require_once 'conf/conf.php';

function uploadImg($fileBox) {

  $imgPath = ATT_IMG_URL_PATH;
  $tmp_file = $_FILES[$fileBox]['tmp_name'];

  if (!is_uploaded_file($tmp_file)) {
    echo "<font size=\"3\" color='red'><strong>Image: File not found</strong></font><br>";
  } else {
    if (preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $name_file)) {
      echo "<font size=\"3\" color='red'><strong>Image: Filename not valid</strong></font><br>";
    } else {
      $type_file = $_FILES[$fileBox]['type'];
      if (!strstr($type_file, 'jpg') && !strstr($type_file, 'jpeg') && !strstr($type_file, 'bmp') && !strstr($type_file, 'gif') && !strstr($type_file, 'png')) {
        echo "<font size=\"3\"  color='red'><strong>Image: not a picture</strong></font><br>";
      } else {
        $name_file = $_FILES[$fileBox]['name'];

        $new_name = strtolower(time() . '-' . $name_file);

        if (!move_uploaded_file($tmp_file, WEB_PATH . $imgPath . $new_name)) {
          echo "<font size=\"3\" color='red'><strong>Image: error while copying file</strong></font><br>";
        } else {
          echo "<font size=\"3\" color='green'><strong>Image: file succesfully uploaded</strong></font><br>";

          return $imgPath . $new_name;
        }
      }
    }
  }
  return null;
}

function uploadDoc($fileBox) {
  $tmp_file = $_FILES[$fileBox]['tmp_name'];

  echo 'Filename' . $_FILES[$fileBox]['name'] . '<br>';
  echo 'Temp file' . $_FILES[$fileBox]['tmp_name'] . '<br>';

  if (!is_uploaded_file($tmp_file)) {
    echo "<font size=\"3\" color='red'><strong>Upload: File not found</strong></font><br>";
  } else {
    if (preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $name_file)) {
      echo "<font size=\"3\" color='red'><strong>Upload: Filename not valid</strong></font><br>";
    } else {
      $name_file = $_FILES[$fileBox]['name'];
      $new_name = time() . '-' . $name_file;
      echo 'Dest name: ' . $new_name . '<br>';
      if (!move_uploaded_file($tmp_file, ATT_FILES_PATH . '/' . $new_name)) {
        echo "<font size=\"3\" color='red'><strong>Upload: error while copying file</strong></font><br>";
      } else {
        echo "<font size=\"3\" color='green'><strong>File succesfully uploaded</strong></font><br>";
        echo "Uploaded file: " . ATT_FILES_PATH . '/' . $new_name;
        return $new_name;
      }
    }
  }
  return null;
}

?>
