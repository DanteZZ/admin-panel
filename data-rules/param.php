<?

class DataRule_param {
	public function compute($data,$all=Array()) {
		GLOBAL $PARAMS;
		$res = $PARAMS->get($data['name']);
		if ($res == null) {$res = $data['isnull'];};
		return $res; 
	}
}

?>