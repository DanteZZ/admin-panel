<?
	GLOBAL $DS, $USR;
	
	if (!$USR->isAuth()) { echo '{"error":"not-auth"}'; exit;};

	$unuse = Array("id","type","creator","editor","create_time","edit_time");
	if (isset($_POST['dataset'])) {
		if (isset($_POST['dataset']['id'])) {
			$res = Array();
			foreach ($_POST['dataset'] as $par=>$val) {
				if (!in_array($par,$unuse)) {
					$res[$par] = $val;
				};
			};
			if ($_POST['full']) {
				$full = true;
			} else {
				$full = false;
			};
			$res = $DS->change($_POST['dataset']['id'],$_POST['dataset']['type'],$res,$full);
			echo json_encode(Array("result"=>$res));
		} else {
			if ($DS->isType($_POST['type'])) {
				$res = $DS->add($_POST['type'],$_POST['dataset']);
				echo json_encode(Array("result"=>$res));
			} else {
				echo json_encode(Array("error"=>"not-correct-type"));
			};
		};
	} else {
		echo json_encode(Array("error"=>"empty-dataset"));
	}
?>