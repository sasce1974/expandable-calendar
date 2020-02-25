<?php
require_once("../includes/functions.php");
if(!isset($_POST['check_email'])){
    die(header("Location: ../register/"));
}
try {
	if(isset($_POST['check_email'])){
		$con = connectPDO();
		$email=filter_input(INPUT_POST,"check_email", FILTER_SANITIZE_EMAIL);
		$query = $con->prepare("SELECT id FROM users WHERE email = ?");
		$query->execute(array($email));
		//$result = $query->fetchAll(PDO::FETCH_ASSOC);
		if($query->rowCount()>0){
			print "<span style='color:#f55;font-size:80%;'>This email is taken &nbsp;&#x2718;</span>";
		}else{
			print "<span style='color:#3b3;font-size:80%;'>This email is available &nbsp;&#x2714;</span>";
		}
	}
} catch(PDOException $e) {
    errorMessage($e);
} // end try