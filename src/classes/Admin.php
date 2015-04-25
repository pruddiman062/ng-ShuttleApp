<?php

class Admin extends utils {
	
	function checkLogin()
	{
		if(isset($_SESSION["USERNAME"]) && isset($_SESSION["SESSIONID"]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function login($username, $password)
	{
		$uname = $this->escSQL($username);
		$pass = md5(strtoupper($username).$password);
		
		$SQL = "SELECT username, firstName, lastName FROM rwu_adminAccounts WHERE username='".$username."' and password = '".$pass."'";
		$result = $this->execSQL($SQL);
		
		if(mysqli_num_rows($result) == 1)
		{
			$record = mysqli_fetch_array($result);
			$_SESSION['USERNAME'] = $record[0];
			$_SESSION['SESSIONID'] = session_id();
			$_SESSION['FIRSTNAME'] = $record[1];
			$_SESSION['LASTNAME'] = $record[2];
			return true;
		}
		else
		{
			return false;
		}
		
	}
	function update_insert($id, $rid, $name, $lat, $long)
	{
		if(!$this->checkLogin())
		{
			return "not logged in";
		}
		$rid = $this->escSQL($rid);
		$id = $this->escSQL($id);
		$name = $this->escSQL($name);
		$lat = $this->escSQL($lat);
		$long = $this->escSQL($long);
		
		if($id == "New")
		{
			$SQL = "INSERT INTO rwu_shuttleStops (stopRegionId, stopName, stopLat, stopLong)";
			$SQL .= " VALUES (".$rid.", '".$name."', '".$lat."', '".$long."');";
		}
		else
		{
			$SQL = "";
			$SQL .= "UPDATE rwu_shuttleStops";
			$SQL .= " SET";
			$SQL .= " stopRegionId = ".$rid.",";
			$SQL .= " stopName = '".$name."',";
			$SQL .= " stopLat = '".$lat."',";
			$SQL .= " stopLong = '".$long."'";
			$SQL .= " WHERE stopId = ".$id.";";
				
		}
		return $this->execSQL($SQL);
	}
	function delete($id)
	{
		if(!$this->checkLogin())
		{
			return "not logged in";
		}
		$id = $this->escSQL($id);
		$SQL = "DELETE FROM rwu_shuttleStops WHERE stopID = ".$id;
		return $this->execSQL($SQL);
	}
	
	
}