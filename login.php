<?php 
	if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
$_SESSION['ID']=NULL;
$_SESSION['level']=-1;
$salt = 'asdflaskfjalsdkfj';
$ip = $_SERVER['REMOTE_ADDR'];


if($_GET['log']=='uit'){//logout
	//destroy session	
	session_unset();     // unset $_SESSION variable for the run-time 
	session_destroy();   // destroy session data in storage.
	$msg = '3 uitgelogd';
	$level = -1;
	header('Location: index.php');
}
else{//login
//if username/password are given, login with them
	
	if(isset($_POST)){
		require 'cons/afdwaka.con';
		$usernm = mysql_real_escape_string(strip_tags(strtolower($_POST['gebruikersnaam'])));
		if($usernm == 'local' || $username == 'localguest')
			$usernm = '';
		$wachtwoord = md5(mysql_real_escape_string(strip_tags($_POST['wachtwoord'])).$salt);
		$query = "SELECT pwdhash, level, fullname FROM users WHERE usernm='$usernm'";
		if(!$resultaat = $db_con->query($query)) trigger_error('Fout in query: '.$db_con->error); 
//		$resultaat = mysqli_query($query)or die (mysql_error());
		$row = $resultaat->fetch_assoc();
		//$row = mysqli_fetch_array($resultaat);
		if($row){
			if($wachtwoord == $row['pwdhash'])
			{
					$id = $row['ID'];
					$level = $row['level'];
					$_SESSION['usernm']=$usernm;
					$_SESSION['IP']=$ip;
					$_SESSION['level']= $level;
					$_SESSION['fullname']=$row['fullname'];
					$_SESSION['CREATED'] = time();
					$_SESSION['LAST_ACTIVITY'] = time();
					$_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
					$query = "insert into logs_users (username, location, level) values ('$usernm','$ip','$level')";
					if(!$resultaat = $db_con->query($query)){
						echo ('Fout in query: '.$db_con->error); 
						exit();
					}
					header("Location: settings.php");
			}
			else{//pswd not matchin
				header('Location: index.php?pswd=error');
			}
		}else{//no entry found in db
			header('Location: index.php?usrnm=error');
		}
	}else{
		echo 'No login, invalid';
		
	}
}
?>