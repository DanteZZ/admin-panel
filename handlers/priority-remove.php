<?
	GLOBAL $PRIORITY;
	if (isset($_POST['tag'])) {
		$res = $PRIORITY->remove($_POST['tag']);
		echo json_encode(Array("result"=>$res));
	} else {
		echo json_encode(Array("error"=>"empty-tag"));
	}
?>