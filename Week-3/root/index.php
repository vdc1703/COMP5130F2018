<!--

-->

<?php include("inc/header.php"); ?>

<div class="container-fluid pad-top-10 bg-project">
	<div class="text-center">
		<h2>Table Dynamics</h2>
		<?php
			$row = 5;
			$col =6;
			
			echo "<center><table>";
			
			for ($i = 0; $i< $row; $i++)
			{
				echo "<tr>";
				for($y = 0; $y<$col; $y++){
					echo "<td>TESTING</td>";
				}
				echo "</tr>";
			}

			echo "</table></center>";

			
		?>

	</div>
</div> 

<?php include("inc/footer.php"); ?>