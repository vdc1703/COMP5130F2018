<?php
require_once "./includes/vdc.php";

if(isset($_GET["name"])) {
    $user_name = htmlspecialchars(mysqli_real_escape_string($conn,$_GET['name']));
    $sql_user = "SELECT user_id FROM `tbl_user` WHERE username = '$user_name' LIMIT 1";
    $result_user = mysqli_query($conn,$sql_user);
    $row = mysqli_fetch_array($result_user,MYSQLI_ASSOC);      
    $count = mysqli_num_rows($result_user);
    
    if($count == 1) {
        $userid = $row['user_id'];      
    }

    if ($logged_username == $user_name) {
        $isauthor = true;
    } else {
        $isauthor = false;
    }      
}

$page_title = $user_name."'s Gallery";
includeHeader('./template/site_header.php', array('page_title' => $page_title));

$sql_img = "SELECT * FROM `tbl_img` WHERE user_id = $userid AND album_id = 0";
$result_img = mysqli_query($conn,$sql_img);

$sql_album = "SELECT alb.album_id AS album_id, alb.album_title, COUNT(p.img_id) AS pcount, MAX(p.img_name) AS thumbnail
FROM tbl_album alb
LEFT JOIN tbl_img p ON p.album_id=alb.album_id
WHERE alb.user_id = $userid
GROUP BY alb.album_id, alb.album_title
ORDER BY alb.album_title ASC";
$result_album = mysqli_query($conn,$sql_album);

require_once "./template/site_body.php";
if ($isauthor || $isadmin) {
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit-data'])) {
    switch ($_POST['submit-data']) {
          case 'createAlbum':    			
    			$album_name = htmlspecialchars(mysqli_real_escape_string($conn,$_POST['album-name']));
                
                $sql = "SELECT album_id FROM tbl_album WHERE album_title = '$album_name' AND user_id = $logged_userid LIMIT 1;";
                $result = mysqli_query($conn,$sql);
                $total = mysqli_num_rows($result); 
                
                if ($total == 1) {
                    $msg = $album_name." is exist, please choose another album name";
                } else {
                    $sql_insert = "INSERT INTO tbl_album (album_title, user_id) VALUES ('$album_name', $logged_userid);";
                    mysqli_query($conn,$sql_insert);
                    echo '<script>window.location.href="gallery.php?name='.$user_name.'"</script>';	
                }
                break;
          case 'deleteImages':
                if ($_POST["user_images"]) {
                    // $user_images = implode(",", $_POST["user_images"]);
                    $user_images = array_filter($_POST["user_images"]);
                    if(!empty($user_images)){
        				foreach ($user_images as $user_image) {
        					unlink($root_path.upload_path.$user_image);
        					$sql= "DELETE FROM `tbl_img` WHERE `img_name` = '$user_image';";
                            mysqli_query($conn,$sql);
        				}                    
                        echo '<script>window.location.href="gallery.php?name='.$user_name.'"</script>';	
                    }
                }
                break;
          case 'moveImages':
                $select_album = $_POST["select-album"];
                if ($_POST["user_images"]) {
                    // $user_images = implode(",", $_POST["user_images"]);
                    $user_images = array_filter($_POST["user_images"]);
                    if(!empty($user_images)){
        				foreach ($user_images as $user_image) {
        					$sql= "UPDATE `tbl_img` SET album_id = $select_album WHERE `img_name` = '$user_image';";
                            mysqli_query($conn,$sql);
        				}                    
                        echo '<script>window.location.href="gallery.php?name='.$user_name.'"</script>';	
                    }
                }
                break;
    }
    }
}
}
?>
<h1 class="my-4 text-center text-lg-left"><?php echo $page_title; ?></h1>
<?php if ($isauthor || $isadmin) { ?>
<button type="button" class="btn btn-success" data-toggle="modal" data-target="#createAlbum">Create Album</button>
<br /><br />
<div style = "font-size:14px; color:#CC0000; margin-top:10px; text-align: center;"><?php echo $msg; ?></div>
<?php } ?>
<div class="row">
    <?php while ($row_album = mysqli_fetch_assoc($result_album)) {
        $album_id = $row_album['album_id'];          
        if ($row_album['thumbnail']) {
            $album_thumb = "images/".$row_album['thumbnail'];
            $album_thumb = resize_img($album_thumb,300,300,'crop');
        } else {
            $album_thumb = "assets/img/no-image.jpg";
        }
    ?>
    <div class="col-lg-3 col-md-4 col-xs-6">
        <a href="#" class="">
            <img class="img-fluid img-thumbnail" src="<?php echo $album_thumb; ?>" />
        </a>
        <div class="title"><?php echo $row_album['album_title']; ?></div>
        <?php if ($isauthor || $isadmin) { ?>
        <div class="action float-right">
            <a href="#deleteAlbum<?php echo $album_id;?>" data-toggle="modal">
                <button class="btn btn-outline-danger float-right btn-sm"><i class="far fa-trash-alt"></i></button>
            </a>
            <a href="#renameAlbum<?php echo $album_id;?>" data-toggle="modal">
                <button type="submit" class="btn btn-outline-secondary float-right btn-sm" name="submit-data" value="deleteImages"><i class="far fa-edit"></i></button>
            </a>     
        </div>
        <?php } ?>       
    </div>
    <?php if ($isauthor || $isadmin) { ?>
    <!--Delete Modal -->
    <div class="modal fade" id="deleteAlbum<?php echo $album_id; ?>" role="dialog">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form method="post">
          <div class="modal-header">
            <h5 class="modal-title" id="createAlbumLabel">Delete Album</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
          <input type="hidden" name="delete_id" value="<?php echo $album_id; ?>">
            <div class="alert alert-danger">Are you Sure you want Delete Album<strong>
            <?php echo $row_album['album_title']; ?>?</strong> </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            <button type="submit" name="deleteAlbum" class="btn btn-success">Yes</button>
          </div>
        </form>
        </div>
      </div>
    </div>
    <!--Edit Item Modal -->
    <div class="modal fade" id="renameAlbum<?php echo $album_id; ?>" role="dialog">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form method="post">
          <div class="modal-header">
            <h5 class="modal-title" id="createAlbumLabel">Rename Album</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <div class="form-group">
                <label for="album-name" class="col-form-label">Album Name:</label>
                <input type="text" name="album_name" value="<?php echo $row_album['album_title']; ?>" class="form-control" id="album-name" placeholder="Enter Album Name" />
                <input type="hidden" name="rename_id" value="<?php echo $album_id; ?>">
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" name="renameAlbum" class="btn btn-success">Rename</button>
          </div>
        </form>
        </div>
      </div>
    </div>            
    <?php 
    if(isset($_POST['deleteAlbum'])){
        // sql to delete a record
        $delete_id = $_POST['delete_id'];
        $sql = "DELETE FROM tbl_album WHERE album_id='$delete_id' ";
        mysqli_query($conn,$sql);
        $sql = "UPDATE `tbl_img` SET album_id = 0 WHERE album_id = '$delete_id';";
        mysqli_query($conn,$sql);
        echo '<script>window.location.href="gallery.php?name='.$user_name.'"</script>';	
    }
    if(isset($_POST['renameAlbum'])){
        // sql to delete a record
        $rename_id = $_POST['rename_id'];
        $album_name = $_POST['album_name'];
        $sql = "UPDATE `tbl_album` SET album_title = '$album_name' WHERE album_id = '$rename_id';";
        mysqli_query($conn,$sql);
        echo '<script>window.location.href="gallery.php?name='.$user_name.'"</script>';	
    }        
    } ?>
<?php } ?>   
</div>
<?php if ($isauthor || $isadmin) { ?>
<form method="post" onsubmit="return Confirm();">
    <button type="submit" class="btn btn-primary" name="submit-data" value="moveImages">Move Images</button>
    <div class="form-check form-check-inline">
        <select id="select-album" name="select-album" class="form-control">
            <option selected>Choose Album</option>
            <?php 
            $sql_album = "SELECT * FROM `tbl_album` WHERE user_id = $logged_userid";
            $result_album = mysqli_query($conn,$sql_album);
            while ($row_album = mysqli_fetch_assoc($result_album)) { ?>
                <option value="<?php echo $row_album['album_id']; ?>"><?php echo $row_album['album_title']; ?></option>
             <?php } ?>
        </select>  
    </div>
   
    <button type="submit" class="btn btn-outline-danger" name="submit-data" value="deleteImages">Delete Image</button>
    <button type="button" onclick="act('select');" class="btn btn-link">Select All</button>
    <br /><br />
<?php } ?>    
    <div class="row text-center text-lg-left">
        <?php while ($row_img = mysqli_fetch_assoc($result_img)) {
            $img_id = $row_img['img_id'];
            $img_title = $row_img['img_title'];
            $img_name = $row_img['img_name'];
            $image = "images/".$img_name;
            $image_thumb = resize_img($image,300,300,'crop');         
        ?>
        <div class="col-lg-3 col-md-4 col-xs-6">
            <a href="<?php echo $image; ?>">
                <img class="img-fluid img-thumbnail" src="<?php echo $image_thumb; ?>" alt="<?php echo $img_title; ?>" />            
            </a>
            <div class="title">
                <?php if ($isauthor || $isadmin) { ?><input type="checkbox" name="user_images[]" class="user_images" value="<?php echo $img_name; ?>"/><?php } ?>  
                <?php echo $img_title; ?>
            </div>
            <?php if ($isauthor || $isadmin) { ?>
            <div class="action float-right">
                <a href="#deleteImage<?php echo $img_id;?>" data-toggle="modal">
                    <button class="btn btn-outline-danger float-right btn-sm"><i class="far fa-trash-alt"></i></button>
                </a>
                <a href="#renameImage<?php echo $img_id;?>" data-toggle="modal">
                    <button type="submit" class="btn btn-outline-secondary float-right btn-sm" name="submit-data" value="deleteImages"><i class="far fa-edit"></i></button>
                </a>     
            </div>
            <?php } ?>              
        </div>
    <?php if ($isauthor || $isadmin) { ?>
    <!--Delete Modal -->
    <div class="modal fade" id="deleteImage<?php echo $img_id; ?>" role="dialog">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form method="post">
          <div class="modal-header">
            <h5 class="modal-title" id="createAlbumLabel">Delete Image</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
          <input type="hidden" name="delete_id" value="<?php echo $img_id; ?>">
            <div class="alert alert-danger">Are you Sure you want Delete Image<strong>
            <?php echo $img_title; ?>?</strong> </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            <button type="submit" name="deleteImage" class="btn btn-success">Yes</button>
          </div>
        </form>
        </div>
      </div>
    </div>
    <!--Edit Item Modal -->
    <div class="modal fade" id="renameImage<?php echo $img_id; ?>" role="dialog">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form method="post">
          <div class="modal-header">
            <h5 class="modal-title" id="createAlbumLabel">Rename Album</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <div class="form-group">
                <label for="image-name" class="col-form-label">Image Name:</label>
                <input type="text" name="image_name" value="<?php echo $img_title; ?>" class="form-control" id="album-name" placeholder="Enter Image Name" />
                <input type="hidden" name="rename_id" value="<?php echo $img_id; ?>">
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" name="renameImage" class="btn btn-success">Rename</button>
          </div>
        </form>
        </div>
      </div>
    </div>            
    <?php 
    if(isset($_POST['deleteImage'])){
        // sql to delete a record
        $delete_id = $_POST['delete_id'];
        $sql = "DELETE FROM tbl_img WHERE img_id='$delete_id' ";
        mysqli_query($conn,$sql);
        echo '<script>window.location.href="gallery.php?name='.$user_name.'"</script>';	
    }
    if(isset($_POST['renameImage'])){
        // sql to delete a record
        $rename_id = $_POST['rename_id'];
        $image_name = $_POST['image_name'];
        $sql = "UPDATE `tbl_img` SET img_title = '$image_name' WHERE img_id = '$rename_id';";
        mysqli_query($conn,$sql);
        echo '<script>window.location.href="gallery.php?name='.$user_name.'"</script>';	
    }        
    } ?>
<?php } ?>     
    </div>
<?php if ($isauthor || $isadmin) { ?>
</form>

<!-- Modal Create Album -->
<div class="modal fade" id="createAlbum" tabindex="-1" role="dialog" aria-labelledby="createAlbumLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
    <form method="post">
      <div class="modal-header">
        <h5 class="modal-title" id="createAlbumLabel">Create Album</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="form-group">
            <label for="album-name" class="col-form-label">Album Name:</label>
            <input type="text" name="album-name" class="form-control" id="album-name" placeholder="Enter Album Name" />
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" name="submit-data" value="createAlbum" class="btn btn-success">Create</button>
      </div>
    </form>
    </div>
  </div>
</div>
<?php } ?> 
<?php require_once "./template/site_footer.php"; ?>
