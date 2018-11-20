<?php
require_once "./includes/vdc.php";

$page_title = "VDC Gallery";
includeHeader('./template/site_header.php', array('page_title' => $page_title));

require_once "./template/site_body.php";

if(!isset($_SESSION['login_user'])){
    header("location:login.php");
}

if(isset($_POST['submit'])){
    
    // File upload configuration
    $targetDir = "images/";
    $allowTypes = array('jpg','png','jpeg','gif');
    
    $statusMsg = $errorMsg = $insertValuesSQL = $errorUpload = $errorUploadType = '';
    if(!empty(array_filter($_FILES['files']['name']))){
        foreach($_FILES['files']['name'] as $key=>$val){
            // File upload path
            $fileName = basename($_FILES['files']['name'][$key]);
            $ext = end((explode(".", $fileName)));
            $targetFilePath = $targetDir . $fileName;
            
            // Check whether file type is valid
            $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
            $fileTitle = pathinfo($targetFilePath, PATHINFO_FILENAME);
            $newfileName = "img_".uniqid().".".$ext;            
               
            if(in_array($fileType, $allowTypes)){
                // Upload file to server
                if(move_uploaded_file($_FILES["files"]["tmp_name"][$key], $targetDir.$newfileName)){
                    // Image db insert sql
                    $insertValuesSQL .= "('".$newfileName."', '".$logged_userid."', '".$fileTitle."'),";
                }else{
                    $errorUpload .= $_FILES['files']['name'][$key].', ';
                }
            }else{
                $errorUploadType .= $_FILES['files']['name'][$key].', ';
            }
        }
        
        if(!empty($insertValuesSQL)){
            $insertValuesSQL = trim($insertValuesSQL,',');
            // Insert image file name into database
            $insert = $conn->query("INSERT INTO tbl_img (img_name, user_id, img_title) VALUES $insertValuesSQL");
            if($insert){
                $errorUpload = !empty($errorUpload)?'Upload Error: '.$errorUpload:'';
                $errorUploadType = !empty($errorUploadType)?'File Type Error: '.$errorUploadType:'';
                $errorMsg = !empty($errorUpload)?'<br/>'.$errorUpload.'<br/>'.$errorUploadType:'<br/>'.$errorUploadType;
                $statusMsg = "Files are uploaded successfully.".$errorMsg;
            }else{
                $statusMsg = "Sorry, there was an error uploading your file.";
            }
        }
    } else{
        $statusMsg = 'Please select a file to upload.';
    }
    
    // Display status message
    echo $statusMsg;
}
?>
<form action="" method="post" enctype="multipart/form-data">
    Select Image Files to Upload:
    <input type="file" name="files[]" multiple />
    <input type="submit" name="submit" value="UPLOAD" />
</form> 
<?php require_once "./template/site_footer.php"; ?>