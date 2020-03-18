<?
	GLOBAL $USR;
	
	if (!$USR->isAuth()) { echo '{"error":"not-auth"}'; exit;};
	echo json_encode(Array("result"=>$USR->remove($_POST['username'])));
?>