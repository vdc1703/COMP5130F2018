<?php
	
	class vdc_mysql_driver
	{
		// Class Initialization Method
		function __construct() { global $vdc; $this->vdc = &$vdc; }
		
		function connect($host, $username, $password, $database, $port, $boolerror = false)
		{
            $this->root_connection = "";
			if (extension_loaded("mysql") == false) {
				echo "mysql php extension not loaded";
			} else {
				$connection_id = mysql_connect("{$host}:{$port}", $username, $password, false);
				
				if (is_resource($connection_id) == false) {
				    echo $boolerror;
				} else {
					if (mysql_select_db($database, $connection_id) == false) {
						echo $boolerror;
					} else {
						if (is_resource($this->root_connection) == false) {
							$this->root_connection = $connection_id;
						}
					}
				}
				
				return $connection_id;
			}
		}
		
		function close()
		{
			if (is_resource($this->root_connection) == true) {
				mysql_close($this->root_connection);
			}
		}
		
		function query($query, $input = NULL, $addon = NULL)
		{
			if (is_resource($this->root_connection) == false) {
				$this->connect($this->vdc->info->config['dbHost'], $this->vdc->info->config['dbUser'], $this->vdc->info->config['dbPass'], $this->vdc->info->config['dbName'], $this->vdc->info->config['dbPort']);
			}
			$this->query_result = mysql_query($query, $this->root_connection);
            
			return ($this->query_result);
		}
		
		function total_rows($query_id)
		{
			return mysql_num_rows($query_id);
		}
		
		function fetch_array($query_id, $result_type = MYSQL_ASSOC)
		{
			return mysql_fetch_array($query_id, $result_type);
		}
	}
	
?>