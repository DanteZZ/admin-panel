<?

class DataRule_ds {
	public function compute($data,$all=Array()) {
		GLOBAL $DS;
		$id = explode("->", $data['id']);

		if ($id[0] == '$_GET') { // Если ИД получаем из GET
			$id = $_GET[$id[1]];
		} else if ($id[0] == '$_POST') { // Если ИД получаем из POST
			$id = $_POST[$id[1]];
		} else { //Иначе просто значение
			$id = $id[0];
		};
		

		$resval = $DS->get($id);

		if ($data['compute']) { //Если нужно обработать какие-то параметры
			$resval = $this->computeVals($data['compute'],$resval);
		};
		return $resval;
	}

	public function computeVals($comp,$data) {
		GLOBAL $DS;
		foreach ($comp as $cpar=>$chow) {
		  if ($cpar == '$') { //Если заменяется вся переменная
		  	$cval = $data;
		  } else {//Если определённые
		  	$cval = $data[$cpar];
		  };
		  
		  $alg = explode("->", $chow);
		  foreach ($alg as $kalg=>$al) {
		    $simple = true;
		    if (mb_substr($al, 0,4) == '$DS(') { //Если запрашивается DataSet
		      $simple = false;
		      preg_match('#\((.*?)\)#', $al, $match);
		      $dsval = $match[1];
		      if ($dsval == '$') {
		        $cval = $DS->get($cval);
		      } else {
		        $cval = $DS->get($cval[$dsval]);
		      };
		    };

		    if ($simple) { //Если просто запрашивается обычные данные
		      $cval = $cval[$al];
		    };
		  };
		  $data[$cpar] = $cval;
		  if ($cpar == '$') { //Если заменяется вся переменная
		  	$data = $cval;
		  } else {//Если определённые
		  	$data[$cpar] = $cval;
		  };
		};
		return $data;
	}
}

?>