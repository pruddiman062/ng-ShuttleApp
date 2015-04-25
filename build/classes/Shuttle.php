<?php

class Shuttle extends utils {
	
	function __construct()
	{
		
	}
	
	function getSwarm()
	{
		
	}
	
	function convertGPS($co_ord)
	{
		/*
			Latitude: N 50 6' 52.7746"
			Longitude:W 94 31' 21.5148"
			Latitude:N 50 6.879576'
			Longitude:W 94 31.35858'
			Latitude:50.11466
			Longitude:-94.522643
		 */
		if(is_float($co_ord))
		{
			return $co_ord;
	 	}
		else
	 	{
	 		$co_ord = preg_replace('([a-bA-Z]\ )?(\��)?(\')?(\")?', '', $co_ord);
	 		$co_ordArray = split(' ', $co_ord);
			
			$degrees = isset($co_ordArray[0])? $co_ordArray[0]:0;
			$minutes = isset($co_ordArray[1])? ($co_ordArray[1]/60):0;
			$seconds = isset($co_ordArray[2])? ($co_ordArray[2]/3600):0;
			
			
			$decimal_co_ord = $degrees + $minutes + $seconds;
			return $decimal_co_ord;
	 	}
	}
		
	function buildJSON()
	{
		$regions = $this->getRegions();
		for($i = 1; $i < sizeof($regions); $i++)
		{
			$regions[$i]["STOPS"] = $this->getStops($i);
			//for($j=1; $j<sizeof($regions[$i]["STOPS"]); $j++)
			foreach($regions[$i]["STOPS"] as $j => $val)
			{
				$regions[$i]["STOPS"][$j][3] = $this->getSchedule($j);
			}
		}
		
		return json_encode($regions);
	}
	
	function getRegions()
	{
		$regionArray = null;
		
		$result = $this->execSQL("SELECT regionId, regionName FROM rwu_Regions");
		while($row = mysqli_fetch_array($result))
		{
			$regionArray[$row[0]]["NAME"] = $row[1];
			$regionArray[$row[0]]["STOPS"] = "";
				
		}
		return $regionArray;
	}
	
	function getStops($regionID, $is_web = false)
	{
		$stopArray = null;
		
		$result = $this->execSQL("SELECT stopID, stopName, x, y, stopLat, stopLong FROM rwu_shuttleStops WHERE stopRegionID = '".$regionID."'");
		while($row = mysqli_fetch_array($result))
		{
			$stopArray[$row[0]][0] = $row[1];
			$stopArray[$row[0]][1][0] = $row[2];
			$stopArray[$row[0]][1][1] = $row[3];
			$stopArray[$row[0]][2][0] = $row[4];
			$stopArray[$row[0]][2][1] = $row[5];
			$stopArray[$row[0]][3] = "";
			
			if($is_web)
			{
				$stopArray[$row[0]][4] = $row[0];
			}
		}
		return $stopArray;
	}
	
	function getSchedule($stopID)
	{
		$scheduleArray = null;
		
		$result = $this->execSQL("SELECT scheduleID, time, date FROM rwu_schedule WHERE StopID = '".$stopID."' ORDER BY time ASC");
		while($row = mysqli_fetch_array($result))
		{
			$scheduleArray[$row[0]][0] = $row[1];
			$scheduleArray[$row[0]][1] = $row[2];
		}
		return $scheduleArray; 
	}
	
	
}




?>
