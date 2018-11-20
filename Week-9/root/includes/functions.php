<?php

	
	class vdc_core_functions
	{
		// Class Initialization Method
		function __construct() { global $vdc; $this->vdc = &$vdc; }
		
		function is_null($string) 
		{
			return ((empty($string) == false && $string !== 0 && $string !== "0") ? false : true);
		}
        
        function includeWithVariables($filePath, $variables = array(), $print = true)
        {
            $output = NULL;
            if(file_exists($filePath)){
                // Extract the variables to a local namespace
                extract($variables);
        
                // Start output buffering
                ob_start();
        
                // Include the template file
                require_once $filePath;
        
                // End buffering and return its contents
                $output = ob_get_clean();
            }
            if ($print) {
                print $output;
            }
            return $output;
        
        }        
		
		function clean_array($array, $noclean = false)
		{
			if (is_array($array) == true && $this->is_null($array) == false) {
				$array = array_change_key_case($array);
				
				// VDC Images Server now sets input data as strings.
				// This is done so that it can work with PHP 5.5 builds.
				
				foreach ($array as $key => $value) {
					$key = (string)$key;
					
					if (is_array($value) == true) {
						$array[$key] = $this->clean_array($value);
					} elseif ($this->is_null($value) == false) {
						$array[$key] = (string)trim(stripslashes($value));
					}
				}
			}
			
			return $array;
		}
		
		function read_file($img_name)
		{
			return @file_get_contents($img_name);	
		}
		
		function write_file($img_name, $content, $flags = NULL)
		{
			return @file_put_contents($img_name, $content, $flags);
		}
		
		function append_file($img_name, $content)
		{
			return $this->write_file($img_name, $content, FILE_APPEND);	
		}
		
		function create_tempfile($content)
		{
			$img_name = md5($this->random_string(20));	
			
			$file_write = $this->write_file("{$this->vdc->info->root_path}tmp/{$img_name}.txt", $content);
			
			return (($file_write == true) ? "{$img_name}.txt" : false);
		}
		
		function is_url($url, $haspath = true)
		{
			$urlparts = parse_url($url);
			
			if ($urlparts == false) {
				trigger_error("is_url(): URL Parse failed.", E_USER_ERROR);	
			} else {
				$pathcheck = (($haspath == true) ? isset($urlparts['path']) : true);
				
				return ((isset($urlparts['scheme']) == true && isset($urlparts['host']) == true && $pathcheck == true) ? true : false);
			}
		}
		
		function sanitize_string($string) 
		{
			// The characters to retain are from: http://www.php.net/manual/en/filter.filters.sanitize.php
			
			return preg_replace("/[^a-zA-Z0-9\!#\$%&'\*\+\-\=\?\^_`\{\|\}~@\.\[\]\/\s]/", NULL, $string);	
		}
		
		function file_exists($img_name)
		{
			return @is_file($img_name);	
		}
		
		function is_file($img_name, $path = NULL, $checkdb = false, $gallery = 0) 
		{
			$empty_path = $this->is_null($path);
			$base_img_name = $this->vdc->image->basename($img_name);
			
			$file_check = $this->file_exists(($empty_path == false) ? ($path.$base_img_name) : $img_name);
			$sql_check = (($checkdb == true) ? $this->vdc->db->total_rows($this->vdc->db->query("SELECT * FROM `[1]` WHERE `img_name` = '[2]' [[1]] LIMIT 1;", array(tbl_img, $base_img_name), array(($gallery > 0) ? " AND `user_id` = '{$gallery}' " : NULL))) : 0);
			
			return (($checkdb == false) ? $file_check : (($file_check == true && $sql_check == 1) ? true : false));
		}
		
		function random_string($max_length = 20, $random_chars = "abcdefghijklmnopqrstuvwxyz0123456789")
		{
			$chararray = array_map("strtolower", str_split($random_chars));
			
			for ($i = 1; $i <= $max_length; $i++)  {
				$random_char = array_rand($chararray);
				$random_string = ($random_string.$chararray[$random_char]);
			}
			
			return str_shuffle($random_string);
		}
		
		function valid_email($email_address) {
            return ((preg_match("/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/", strtolower($email_address)) == true) ? true : false);
		}
		
		function fetch_url($base = true, $www = true, $query = true)
		{
			$url_scheme = ((IS_HTTPS_REQUEST == true) ? "https://" : "http://");
			$url_img_name = (($base == true) ? pathinfo($this->vdc->input->server_vars['php_self'], PATHINFO_BASENAME) : NULL);
			$url_path = ((($path = pathinfo($this->vdc->input->server_vars['php_self'], PATHINFO_DIRNAME)) !== "/") ? sprintf("%s/", $path) : $path); 
			$url_query = (($query == true && isset($this->vdc->input->server_vars['query_string']) == false) ? "{$the_url}?{$this->vdc->input->server_vars['query_string']}" : NULL);
			$url_host = (($www == true && stripos($this->vdc->input->server_vars['http_host'], "www.") === false) ? "www.{$this->vdc->input->server_vars['http_host']}" : $this->vdc->input->server_vars['http_host']);
		
			return ($url_scheme.$url_host.$url_path.$url_img_name.$url_query); 
		}
	}
	
?>