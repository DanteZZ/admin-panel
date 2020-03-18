<?
	GLOBAL $USR;
	
	if (!$USR->isAuth()) { echo '{"error":"not-auth"}'; exit;};

	if (isset($_POST['user'])) {
		$user = $_POST['user'];
		if (isset($_POST['user']['id'])) {
			$USR->setParameter("name",$user['parameters']['name'],$user['username']);
			$USR->setParameter("email",$user['email'],$user['username']);
			$USR->setParameter("priority",$user['priority'],$user['username']);
			$USR->setParameter("avatar",$user['avatar'],$user['username']);
			
			if (strlen($user['password']) > 0) {
				$USR->setParameter("password",$user['password'],$user['username']);
			}
			echo json_encode(Array("result"=>$user['username']));
		} else {
			$res = $USR->register($user['username'],$user['password'],$user['priority'],$user['email'],$user['parameters'],$user['avatar']);
			if ($res === true) {
				echo json_encode(Array("result"=>$user['username']));
			} else {
				echo json_encode(Array("error"=>$res));
			};
		};
	} else {
		echo json_encode(Array("error"=>"empty-user"));
	}
?>