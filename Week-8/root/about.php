<?php
require_once "./includes/vdc.php";

$page_title = "About Me";
$vdc->funcs->includeWithVariables('./template/site_header.php', array('page_title' => $page_title));
?>
<div class="col-lg-8 col-md-10 mx-auto">
          <p>Still Working On.</p>
        </div>
<?php require_once "./template/site_footer.php"; ?>