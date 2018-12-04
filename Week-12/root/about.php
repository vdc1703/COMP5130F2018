<?php
require_once "./includes/vdc.php";

$page_title = "About Me";
includeHeader('./template/site_header.php', array('page_title' => $page_title));

require_once "./template/site_body.php";
?>
<div class="col-lg-8 col-md-10 mx-auto">
          <p>If you have any question, please contact: <b>Chuong_Vu@student.uml.edu</b></p>
        </div>
<?php require_once "./template/site_footer.php"; ?>