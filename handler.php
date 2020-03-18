<?
	GLOBAL $EVENT;

	$handlers = Array();

	foreach (array_diff(scandir($panelPath."handlers/"), array('.', '..')) as $rkey=>$file) {
      $handlers[str_replace(".php", "", $file)] = $panelPath."handlers/".$file;
    };

	$thirds = $EVENT->init("panel_init",true);
    if ($thirds) {
      foreach ($thirds as $key=>$thr) { //Перебираем все сторонние данные
        if ($thr['handlers']) { //Перебираем компоненты
          foreach (array_diff(scandir($_SERVER['DOCUMENT_ROOT']."/".$thr['handlers']), array('.', '..')) as $rkey=>$file) {
            $handlers[str_replace(".php", "", $file)] = $_SERVER['DOCUMENT_ROOT'].$thr['handlers']."/".$file;
          };
        };
      }
    };
    
	if ($handlers[$action]) { //Если такой обработчик существует
		if (is_file($handlers[$action])) {
			require_once($handlers[$action]);
		} else {
			echo json_encode(Array("error"=>"not-exists-handler-file"));
		};
	} else {
		echo json_encode(Array("error"=>"not-exists-handler"));
	};

?>