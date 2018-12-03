<?php
require_once "./includes/vdc.php";

$page_title = "VDC Gallery";
includeHeader('./template/site_header.php', array('page_title' => $page_title));
    
$sql = "SELECT user.user_id AS user_id, user.username, COUNT(p.img_id) AS pcount, MAX(p.img_name) AS thumbnail
FROM tbl_user user
LEFT JOIN tbl_img p ON p.user_id=user.user_id
GROUP BY user.user_id, user.username
ORDER BY user.username ASC";
$result = mysqli_query($conn,$sql);

require_once "./template/site_body.php";
?>
<div class="row text-center text-lg-left">
    <?php while ($row = mysqli_fetch_assoc($result)) {          
        if ($row['thumbnail']) {
            $image_thumb = "images/".$row['thumbnail'];
            $image_thumb = resize_img($image_thumb,300,300,'crop');
        } else {
            $image_thumb = "assets/img/no-image.jpg";
        }        
    ?>

    <div class="col-lg-3 col-md-4 col-xs-6">
        <a href="gallery/<?php echo $row['username']; ?>" class="d-block mb-4 h-100">
            <img class="img-fluid img-thumbnail" src="<?php echo $image_thumb; ?>" />
            <?php echo $row['username']; ?>
        </a>
    </div>
    
    <?php } ?>
</div>
<?php require_once "./template/site_footer.php"; ?>