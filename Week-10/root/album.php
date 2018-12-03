<?php
require_once "./includes/vdc.php";

$page_title = "Gallery";
includeHeader('./template/site_header.php', array('page_title' => $page_title));

if(!isset($_SESSION['login_user'])){
    header("location:login.php");
}

$sql_img = "SELECT * FROM `tbl_img` WHERE user_id = $logged_userid";
$result_img = mysqli_query($conn,$sql_img);

$sql_album = "SELECT * FROM `tbl_album` WHERE user_id = $logged_userid";
$result_album = mysqli_query($conn,$sql_album);

require_once "./template/site_body.php";

if($_SERVER["REQUEST_METHOD"] == "POST") {
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
                    $msg = "sucess";		
                }
                break;
          case 'subtract':
                echo $_POST['number_1'] . " - " . $_POST['number_2'] . " = " . ($_POST['number_1']-$_POST['number_2']);
                break;
          case 'multiply':
                echo $_POST['number_1'] . " x " . $_POST['number_2'] . " = " . ($_POST['number_1']*$_POST['number_2']);
                break;
    }
}
?>
<div style = "font-size:14px; color:#CC0000; margin-top:10px; text-align: center;"><?php echo $msg; ?></div>
<h1 class="my-4 text-center text-lg-left"><?php echo $logged_username."'s ".$page_title; ?></h1>

<button type="button" class="btn btn-success" data-toggle="modal" data-target="#createAlbum">Create Album</button>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editAlbum">Edit Album</button>
<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAlbum">Delete Album</button>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editAlbum">Edit Image</button>
<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAlbum">Delete Image</button>

<div class="row text-center text-lg-left">
    <?php while ($row_album = mysqli_fetch_assoc($result_album)) { ?>

    <div class="col-lg-3 col-md-4 col-xs-6">
        <a href="<?php echo $image; ?>" class="d-block mb-4 h-100">
            <img class="img-fluid img-thumbnail" src="assets/img/no-image.jpg" />
            <?php echo $row_album['album_title']; ?>
            <input type="checkbox" name="checked_id[]" class="checkbox" value="<?php echo $row_album['album_id']; ?>"/>
        </a>
    </div>
    
    <?php } ?>
</div>

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
<!-- Modal Edit Album -->
<div class="modal fade" id="editAlbum" tabindex="-1" role="dialog" aria-labelledby="editAlbumLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
    <form method="post">
      <div class="modal-header">
        <h5 class="modal-title" id="editAlbumLabel">Edit Album</h5>
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
        <button type="submit" name="submit-data" value="editAlbum" class="btn btn-success">Edit</button>
      </div>
    </form>
    </div>
  </div>
</div>
<?php require_once "./template/site_footer.php"; ?>