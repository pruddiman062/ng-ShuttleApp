<?php
class utils{
	/* UTILS */
	function connectToSQL()
	{
		return mysqli_connect("pjrweb.com", "shuttle_app", "Er7B2SCaZecMZzxs", "shuttle_app");
	}
	
	function execSQL($query)
	{
		$connection = $this->connectToSQL();
		if(mysqli_errno($connection))
		{
			throw new Exception("Failed to connect to SQL",0);
		}
	
		$return = mysqli_query($connection, $query);
	
		mysqli_close($connection);
		if($return === false)
		{
			throw new Exception("Error processing sql query", 0);
		}
		else
		{
			if($return === true)
			{
	
			}
			else
			{
				return $return;
			}
		}
	
	}
	
	function escSQL($string)
	{
		$connection = $this->connectToSQL();
		if(mysqli_errno($connection))
		{
			throw new Exception("Failed to connect to SQL",0);
		}
		
		$return = mysqli_real_escape_string($connection, $string);
		
		mysqli_close($connection);
		
		return $return;
	}
}
?>