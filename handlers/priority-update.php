<?
	GLOBAL $PRIORITY;
	if (isset($_POST['priority'])) {
		if (isset($_POST['priority']['id'])) {
			if ($_POST['priority']['name']) {
				$PRIORITY->setName($_POST['priority']['tag'],$_POST['priority']['name']);
			};
			$PRIORITY->setRules($_POST['priority']['tag'],$_POST['priority']['rules']);
		} else {
			if ($_POST['priority']['tag']) {
				if (!$_POST['priority']['rules']) {$_POST['priority']['rules'] = Array();};
				$PRIORITY->add($_POST['priority']['tag'],$_POST['priority']['name'],$_POST['priority']['rules']);
				echo json_encode(Array("result"=>$_POST['priority']['tag']));
				exit;
			} else {
				echo json_encode(Array("error"=>"empty-<priority>-<tag>"));
				exit;
			};
		};
		echo json_encode(Array("result"=>"true"));
	} else {
		echo json_encode(Array("error"=>"empty-<priority>"));
	}
?>