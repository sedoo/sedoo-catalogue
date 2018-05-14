<?php

require_once 'conf/conf.php';

function uploadImg($fileBox) {

  $imgPath = ATT_IMG_URL_PATH;
  $tmp_file = $_FILES[$fileBox]['tmp_name'];

  if (!is_uploaded_file($tmp_file)) {
    echo "<span class='danger'><strong>Image: File not found</strong></span><br>";
  } else {
    if (preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $name_file)) {
      echo "<span class='danger'><strong>Image: Filename not valid</strong></span><br>";
    } else {
      $type_file = $_FILES[$fileBox]['type'];
      if (!strstr($type_file, 'jpg') && !strstr($type_file, 'jpeg') && !strstr($type_file, 'bmp') && !strstr($type_file, 'gif') && !strstr($type_file, 'png')) {
        echo "<span class='danger'><strong>Image: not a picture</strong></span><br>";
      } else {
        $name_file = $_FILES[$fileBox]['name'];

        $new_name = strtolower(time() . '-' . $name_file);

        if (!move_uploaded_file($tmp_file, WEB_PATH . $imgPath . $new_name)) {
          echo "<span class='danger'><strong>Image: error while copying file</strong></span><br>";
        } else {
          echo "<span class='success'><strong>Image: file succesfully uploaded</strong></span><br>";

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
    echo "<span class='danger'><strong>Upload: File not found</strong></span><br>";
  } else {
    if (preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $name_file)) {
      echo "<span class='danger'><strong>Upload: Filename not valid</strong></span><br>";
    } else {
      $name_file = $_FILES[$fileBox]['name'];
      $new_name = time() . '-' . $name_file;
      echo 'Dest name: ' . $new_name . '<br>';
      if (!move_uploaded_file($tmp_file, ATT_FILES_PATH . '/' . $new_name)) {
        echo "<span class='danger'><strong>Upload: error while copying file</strong></span><br>";
      } else {
        echo "<span class='success'><strong>File succesfully uploaded</strong></span><br>";
        echo "Uploaded file: " . ATT_FILES_PATH . '/' . $new_name;
        return $new_name;
      }
    }
  }
  return null;
}

?>
