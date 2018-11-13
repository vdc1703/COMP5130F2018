<?php
require_once "./includes/vdc.php";

$page_title = "Gallery";
$vdc->funcs->includeWithVariables('./template/site_header.php', array('page_title' => $page_title));
    
$sql = $vdc->db->query("SELECT * FROM `tbl_img`");
?>
<h1 class="my-4 text-center text-lg-left"><?php echo $page_title; ?></h1>
<div class="row text-center text-lg-left">
    <?php while ($result = $vdc->db->fetch_array($sql)) { ?>

    <div class="col-lg-3 col-md-4 col-xs-6">
        <a href="#" class="d-block mb-4 h-100">
            <img class="img-fluid img-thumbnail" src="<?php echo "images/".$result['img_name']; ?>" alt="<?php echo $result['img_title']; ?>" />
        </a>
    </div>
    
    <?php } ?>
</div>
<?php require_once "./template/site_footer.php"; ?>