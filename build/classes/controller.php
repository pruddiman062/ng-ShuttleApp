<?php
	if (!isset($_SESSION))
	{
		session_start();
	}
	require_once 'utils.php';
	require_once 'Admin.php';
	require_once 'Shuttle.php';
	
	$adminControl = new Admin();
	
	if(!$adminControl->checkLogin())
	{
		//return "Not Logged in!";
		//exit(0);
	}
	
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	
	
	$action = $request->action;
	switch($action)
	{
		case "status":
			echo json_encode($adminControl->checkLogin());
			break;
		case "login":
			$user = $request->username;
			$pass = $request->password;
			if($adminControl->login($user, $pass))
			{
				echo '{"FIRSTNAME":"'.$_SESSION['FIRSTNAME'].'", "LASTNAME":"'.$_SESSION['LASTNAME'].'"}';
			}
			else 
			{
				echo "false";
			}
			break;
		case "logout":
			session_destroy();
			break;
		case "getuser":
			echo '{"FIRSTNAME":"'.$_SESSION['FIRSTNAME'].'", "LASTNAME":"'.$_SESSION['LASTNAME'].'"}';
			break;
		case "update_insert":
			$id = $request->id;
			$rid = $request->rid;
			$name = $request->name;
			$lat = $request->lat;
			$long = $request->long;
			echo $adminControl->update_insert($id, $rid, $name, $lat, $long);
		break;
		case "delete":
			$id = $request->id;
			echo $adminControl->delete($id);
		break;
		case 'json_getstops':
			$regionid = $request->regionid;
            $oShuttle = new Shuttle();
			$stops = $oShuttle->getStops($regionid, true);
			echo json_encode($stops);
            break;
        case 'json_getschedule':
        	$oShuttle = new Shuttle();
        	$stopID = $request->stopid;
        	$schedule = $oShuttle->getSchedule($stopID);
        	echo json_encode($schedule);
        	break;
       	default:
			echo "Action was incorrect";
			break;
	}
exit(0);

?>