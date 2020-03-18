<?
	GLOBAL $PARAMS;
	if (isset($_POST['name'])) {
		$res = $PARAMS->remove($_POST['name']));
		echo json_encode(Array("result"=>$res));
	} else {
		echo json_encode(Array("error"=>"empty-name"));
	}
?>