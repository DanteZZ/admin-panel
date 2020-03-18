<?
	GLOBAL $DS;

	if (isset($_POST['dataset'])) {
		
		if ($DS->isType($_POST['dataset']['type'])) {
			$res = $DS->add($_POST['dataset']['type'],$_POST['dataset']);
			echo json_encode(Array("result"=>$res));
		} else {
			echo json_encode(Array("error"=>"not-correct-type"));
		};
	} else {
		echo json_encode(Array("error"=>"empty-dataset"));
	}
?>