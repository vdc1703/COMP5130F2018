<!--  
	Update new upload functions. Use DropzoneJ
-->
<?php
require_once "./includes/vdc.php";

$page_title = "VDC Gallery";
includeHeader('./template/site_header.php', array('page_title' => $page_title));

require_once "./template/site_body.php";

if(!isset($_SESSION['login_user'])){
    header("location:login.php");
}
    
if(!empty($_FILES)) {
    $fileName = basename($_FILES['file']['name']);
    $ext = end((explode(".", $fileName)));
    $targetFilePath = upload_path . $fileName;
    $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
    $fileTitle = pathinfo($targetFilePath, PATHINFO_FILENAME);
    $newfileName = "img_".uniqid().".".$ext;
    
    if(move_uploaded_file($_FILES["file"]["tmp_name"], upload_path.$newfileName)){
        // Image db insert sql
        $insertValuesSQL .= "('".$newfileName."', '".$logged_userid."', '".$fileTitle."'),";
    }
    
    if(!empty($insertValuesSQL)){
        $insertValuesSQL = trim($insertValuesSQL,',');
        // Insert image file name into database
        $insert = $conn->query("INSERT INTO tbl_img (img_name, user_id, img_title) VALUES $insertValuesSQL");
    }            
    
}
  
?>
<form action="upload.php" class="dropzone" id="my-awesome-dropzone"></form>
<script type="text/javascript">
    Dropzone.options.myAwesomeDropzone = {
        maxFilesize: 10, // MB
        resizeWidth: 1024,
        acceptedFiles: 'image/*'
    };
</script>    
      
<!--     
<form method="post" enctype="multipart/form-data" class="dropzone" id="my-awesome-dropzone">
    Select Image Files to Upload:
    <input type="file" name="files[]" multiple />
    <input type="submit" name="submit" value="UPLOAD" />
</form>
-->
<?php require_once "./template/site_footer.php"; ?>