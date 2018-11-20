<?php
require_once "./includes/vdc.php";

$page_title = "Gallery";
includeHeader('./template/site_header.php', array('page_title' => $page_title));

if(!isset($_SESSION['login_user'])){
    header("location:login.php");
}

$sql = "SELECT img_name, img_title, album_id FROM `tbl_img` WHERE user_id = $logged_userid";
$result = mysqli_query($conn,$sql);

require_once "./template/site_body.php";
?>
<h1 class="my-4 text-center text-lg-left"><?php echo $logged_username."'s ".$page_title; ?></h1>
<div class="row text-center text-lg-left">
    <?php while ($row = mysqli_fetch_assoc($result)) {
        $image = "images/".$row['img_name'];
        $image_thumb = resize_img($image,150);         
    ?>

    <div class="col-lg-3 col-md-4 col-xs-6">
        <a href="<?php echo $image; ?>" class="d-block mb-4 h-100">
            <img class="img-fluid img-thumbnail" src="<?php echo $image_thumb; ?>" alt="<?php echo $row['img_title']; ?>" />
        </a>
    </div>
    
    <?php } ?>
</div>
<?php require_once "./template/site_footer.php"; ?>