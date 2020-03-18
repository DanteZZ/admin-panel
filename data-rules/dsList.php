<?

class DataRule_dsList {
	public function compute($data,$all=Array()) {
		  GLOBAL $DS;
		  $DR_DS = new DataRule_ds;
	      if (!$data['parameters']) { //Если запрашивается простой список
	        $resval = $DS->list($data['type']);
	      } else {
	        foreach ($data['parameters'] as $pk =>$pp) {
	          if (stripos($pp, "{") !== false) { //Если значение извне
	            $prep = $pp;
	            $comps = preg_split('/\{|\}(, *)?/',$prep,-1,PREG_SPLIT_NO_EMPTY);
	            foreach ($comps as $ckey=>$comp) {
	              $comp = explode("->",$comp);
	              $pval = $data[$comp[0]];
	              array_shift($comp);
	              if ($comp) {
	                foreach ($comp as $ppk=>$ppv) {
	                  $pval = $pval[$ppv];
	                };
	              };
	              $prep = str_replace("{".$comps[$ckey]."}", $pval, $prep);
	            };
	            $pp = $prep;
	          };
	          $data['parameters'][$pk] = $pp;
	        };
	        $resval = $DS->list($data['type'],$data['parameters']);
	      };
	      if ($data['compute']) { //Если нужно обработать какие-то параметры
	        if ($resval) { //Если есть хоть какие-то данные
	          foreach ($resval as $reskey=>$resrow) {
	            $resval[$reskey] = $DR_DS->computeVals($data['compute'],$resrow);
	          };
	        };
	      };
	      return $resval;
	}
}

?>