<?
	GLOBAL $PARAMS;
	if (isset($_POST['name'])) {
		$res = $PARAMS->set($_POST['name'],$_POST['value'],boolval($_POST['multilang']));
		echo json_encode(Array("result"=>$res));
	} else {
		echo json_encode(Array("error"=>"empty-name"));
	}
?>