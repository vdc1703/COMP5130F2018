<?php
	
	header("Content-Type: image/png;");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT;");
    header("Cache-Control: no-cache, must-revalidate;"); 
	header(sprintf("Content-Disposition: inline; filename=file_rating_%s.png;", mt_rand(1000, 9999)));
	
	if ($vdcclass->funcs->is_file($vdcclass->input->get_vars['file'], $vdcclass->info->root_path.$vdcclass->info->config['upload_path'], true) == false) {
		readfile("{$vdcclass->info->root_path}css/images/ratings/00000.png");
	} else {
		$filename = $vdcclass->image->basename($vdcclass->input->get_vars['file']);
		
		$sql = $vdcclass->db->query("SELECT * FROM `[1]` WHERE `filename` = '[2]' LIMIT 1;", array(MYSQL_FILE_RATINGS_TABLE, $filename));
		
		if ($vdcclass->db->total_rows($sql) == 1) {
			// 0 = empty star; 1 = half star; 2 = full star;
			
			$rating_stars = array(
				  "00000" => array(0.00, 0.00, 0.50),
				  "10000" => array(0.50, 0.50, 0.99),
				  "20000" => array(1.00, 1.50, 1.49),
				  "21000" => array(1.50, 1.50, 1.99),
				  "22000" => array(2.00, 2.00, 2.49),
				  "22100" => array(2.50, 2.50, 2.99),
				  "22200" => array(3.00, 3.00, 3.49),
				  "22210" => array(3.50, 3.50, 3.99),
				  "22220" => array(4.00, 4.00, 4.49),
				  "22221" => array(4.50, 4.50, 4.99),
				  "22222" => array(5.00, 5.00, 5.49),
			);
			
			$rating_results = $vdcclass->db->fetch_array($sql);
			
			if ($rating_results['total_votes'] >= 1) {
				$rating_total = ($rating_results['total_rating'] / $rating_results['total_votes']);
				
				foreach ($rating_stars as $star => $matches) {
					if ($rating_total >= $matches['0'] || $rating_total == $matches['1'] && $rating_total <= $matches['2']) {
						$rating_filename = $star;
					}
				}
				
				readfile("{$vdcclass->info->root_path}css/images/ratings/{$rating_filename}.png");
			} else {
				readfile("{$vdcclass->info->root_path}css/images/ratings/00000.png");
			}
		} else {
			readfile("{$vdcclass->info->root_path}css/images/ratings/00000.png");
		}
	}
	
	exit;
	
?>