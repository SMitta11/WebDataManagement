<pre>
<?php
// put your generated access token here
$auth_token = 'sl.BSv15yACz2bdKrnBhxNs_mr01AT9q3xiemhUI4FWkTVnTIuyhQADfeSKp_tPHSUWps8Zrjt_BAGCiRreik3pipTBIEOA7PZ1RqtmES2fWRnG0sPTzFBY-ROUC4-v1v8Kgj8sNgcTuIVq';

// import the Dropbox library
include "dropbox.php";

// set it to true to display debugging info
$debug = false;

// display all errors on the browser
//error_reporting(E_ALL);
ini_set('display_errors', 'O');

// create a new Dropbox folder called images
createFolder("images");

// upload a local file into the Dropbox folder images
// upload("leonidas.jpg", "/images");

// print the files in the Dropbox folder images
// $result = listFolder("/images");
// foreach ($result['entries'] as $x) {
//    echo $x['name'], "\n";
// }

// download a file from the Dropbox folder images into the local directory tmp
// download("/images/leonidas.jpg", "tmp/tmp.jpg");

// delete a Dropbox file
// delete("/images/leonidas.jpg");

?>

</pre>
<?php



if ($_SERVER["REQUEST_METHOD"] == "POST") {

   //echo "submit image";
   $file = $_FILES['fileToUpload']['name'];
   $ext = pathinfo($file, PATHINFO_EXTENSION);

   //echo $ext;
   if ($ext == 'jpg'||$ext == 'jpeg'|| $ext == 'png')

{
   $target_file =  basename($_FILES["fileToUpload"]["name"]);
   //move file to local directory
   move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
   
   upload($file, '/images');

   //delete file after upload from local directory
   unlink($file);

  }
  else
  {
   echo("Only jpg,png and jpeg files are allowed");
  }
}
  
  




$result = listFolder("/images");
$deleteimageid = $_GET["deleteimageid"];
foreach ($result['entries'] as $y) {
   if ($y['id'] == $deleteimageid) {
      $URL_delete = $y['path_display'];
      delete($URL_delete);
   }
}

$result = listFolder("/images");

?>
<form enctype="multipart/form-data" action="album.php" method="POST">

   <input name="fileToUpload" id="fileToUpload" type="file" /><br />
   <input type="submit" value="Upload File" />
</form>
<div class="flex-container">

   <div class="flex-child1">
      <h2> List of Images in Dropbox </h2>
      <?php
      foreach ($result['entries'] as $x) {
         //$URL_folder=$x['path_display'];
      ?>
         <div>
            <a href="album.php?deleteimageid=<?= $x['id'] ?>">
               <button type="button">Delete</button>
            </a>
            <a href="album.php?imageid=<?= $x['id'] ?>">
               <?= $x['name'] ?>
            </a>
         </div>


      <?php
      }
      ?>
   </div>
   <div class="flex-child2">
      <h2>Image</h2>
      <?php

      if ($_SERVER["REQUEST_METHOD"] == "GET") {
         $imageid = $_GET["imageid"];
         foreach ($result['entries'] as $value) {
            if ($value['id'] == $imageid) {
               $URL_download = $value['path_display'];
               download($URL_download, "tmp/tmp.jpg");
      ?>

               <div><img src="tmp/tmp.jpg" alt="" width="400" height="300"></div>

      <?php
            }
         }
      }
      ?>
   </div>

</div>
<style type="text/css">
   .flex-container {
      display: flex;
   }

   .flex-child1 {
      border-right: 1px solid;

   }

   .flex-child1,
   .flex-child2 {
      padding: 20px;
   }
</style>