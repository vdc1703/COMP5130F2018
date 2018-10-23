<?php include("inc/header.php"); ?>

<!-- Skills -->
<div class="container-fluid pad-top-10 bg-project">
	<div class="text-center">
		<h2>Users</h2>
		<?php

			include('inc/config.php');
			require_once("inc/pmysqlacc.php");
			//get current url linl
			$page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			


			$sql = "select username from user";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {

					echo "<a href='". $page_url . "/users/user.php?id=" . $row["username"]. "'>" . $row["username"] . "</a>  ";
				}
			} else {
				echo "0 resu123lts";
			}
			

			
		?>

	</div>
</div>  

<?php include("inc/footer.php"); ?>