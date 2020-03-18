<?
	GLOBAL $DS;
	if (isset($_POST['id'])) {
		$res = $DS->remove($_POST['id']);
		echo json_encode(Array("result"=>$res));
	} else {
		echo json_encode(Array("error"=>"empty-id"));
	}
?>