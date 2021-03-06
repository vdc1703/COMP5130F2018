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
    $allowTypes = array('jpg','png','jpeg','gif');
    
    $statusMsg = $msgMsg = $insertValuesSQL = $msgUpload = $msgUploadType = '';
    $files = array_filter($_FILES['files']['name']);
    if(!empty($files)){
        foreach($_FILES['files']['name'] as $key=>$val){
            // File upload path
            $fileName = basename($_FILES['files']['name'][$key]);
            $ext = end((explode(".", $fileName)));
            $targetFilePath = upload_path . $fileName;
            
            // Check whether file type is valid
            $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
            $fileTitle = pathinfo($targetFilePath, PATHINFO_FILENAME);
            $newfileName = "img_".uniqid().".".$ext;            
               
            if(in_array($fileType, $allowTypes)){
                // Upload file to server
                if(move_uploaded_file($_FILES["files"]["tmp_name"][$key], upload_path.$newfileName)){
                    // Image db insert sql
                    $insertValuesSQL .= "('".$newfileName."', '".$logged_userid."', '".$fileTitle."'),";
                }else{
                    $msgUpload .= $_FILES['files']['name'][$key].', ';
                }
            }else{
                $msgUploadType .= $_FILES['files']['name'][$key].', ';
            }
        }
        
        if(!empty($insertValuesSQL)){
            $insertValuesSQL = trim($insertValuesSQL,',');
            // Insert image file name into database
            $insert = $conn->query("INSERT INTO tbl_img (img_name, user_id, img_title) VALUES $insertValuesSQL");
            if($insert){
                $msgUpload = !empty($msgUpload)?'Upload Error: '.$msgUpload:'';
                $msgUploadType = !empty($msgUploadType)?'File Type Error: '.$msgUploadType:'';
                $msgMsg = !empty($msgUpload)?'<br/>'.$msgUpload.'<br/>'.$msgUploadType:'<br/>'.$msgUploadType;
                $statusMsg = "Files are uploaded successfully.".$msgMsg;
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