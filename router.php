<?
	/* ------ Подгрузка данных ------ */

	GLOBAL $components,$panelCFG,$templatesPath,$themeLink, $pageInfo, $tempLink, $login_error, $USR, $PRIORITY, $EVENT;


	// Инициализация переменных
	$login_error = false;
	$components_folders = Array();
	$pages_folders = Array($panelPath."pages");


	//Подключение продвинутых переменных
	$panelCFG = json_decode(file_get_contents($panelPath."configs/config.json"),true);
	$templatesPath = $panelPath."themes/".$panelCFG['theme']."/templates/";
	$templatesLink = $panelLink."themes/".$panelCFG['theme']."/templates/";
	$themeLink = $panelLink."themes/".$panelCFG['theme']."/";
	
	foreach (array_diff(scandir($panelPath."vue-components"), array('.', '..')) as $key=>$name) {
		$components_folders[$name] = str_replace($_SERVER['DOCUMENT_ROOT'], "", $panelPath."vue-components/".$name);
	};


	//Проверка сторонних страниц и компонентов
	$thirds = $EVENT->init("panel_init",true);

	if ($thirds) {
		foreach ($thirds as $key=>$thr) { //Перебираем все сторонние данные
			if ($thr['components']) { //Перебираем компоненты
				foreach (array_diff(scandir($_SERVER['DOCUMENT_ROOT']."/".$thr['components']), array('.', '..')) as $key=>$name) {
					$components_folders[$name] = $thr['components']."/".$name;
				};
			};
			if ($thr['pages']) { //Перебираем компоненты
				$pages_folders[] = $_SERVER['DOCUMENT_ROOT'].$thr['pages'];
			};
		}
	};

	// Проверка авторизации
	if (isset($_POST['proccess_login'])) {
	      if ($USR->login($_POST['username'],$_POST['password']) == true) {
	          header("location: ?");
	      } else {
	        $login_error = "Неверный логин или пароль";
	      };
	};

	// Проверка на права
	$usercan = false;
    foreach ($panelCFG['rules'] as $k=>$rule) {
      if ($PRIORITY->isRule($rule)) {$usercan=true; break;}
    };

    if (!$usercan) {
    	if (!$login_error) {
    		$login_error = "Недостаточно прав у пользователя <b>".$_POST['username']."</b>";
    	};
    	require_once($templatesPath."login/index.php");
    	exit;
    };

	if (!$components_folders) {
		echo "<meta charset='UTF-8'><h1>Нет зарегестрированных компонентов, проверьте директорию /vue-components</h1>";
		exit;
	};

	foreach ($components_folders as $key=>$folder) {
		$component = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].$folder."/info.json"),true);
		$component['source'] = $folder."/".$component['file'];
		$components[$component['name']] = $component;
	};

	if (!$panelURL[0]) {
		header("Location: /".$PARAMS->get("panel_link")."/".$panelCFG['default-page']);
	};

	/* ------ Генерация страницы ------ */
	$panelURL[0] = str_replace("/".$PARAMS->get("panel_link")."/","",parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));


	$page = false;

	foreach ($pages_folders as $key=>$path) {
		if (is_file($path."/".$panelURL[0].".tmpl")) { $page = $path."/".$panelURL[0].".tmpl"; };
	};

	if ($page) { //Если запрашиваемая страница существует
		$pageTmpl = file_get_contents($page);

		$infstart = mb_strpos($pageTmpl,'<_info>');
		$infend = mb_strpos($pageTmpl,'</_info>')+8;
		$info = '<?xml version="1.0" encoding="UTF-8"?>'.mb_substr($pageTmpl,$infstart,$infend);
		$pageInfo = json_decode(json_encode(simplexml_load_string($info)),true);

		$tmplstart = mb_strpos($pageTmpl,'<_template>')+11;
		$tmplend = mb_strpos($pageTmpl,'</_template>');
		$pageInfo['_template'] = mb_substr($pageTmpl,$tmplstart,$tmplend-$tmplstart);

		$cssstart = mb_strpos($pageTmpl,'<style>');
		$cssend = mb_strpos($pageTmpl,'</style>');
		
		if ($cssstart !== false) {
			$pageInfo['style'] = mb_substr($pageTmpl,$cssstart+7,$cssend-$cssstart-7);
		};
		


		$vuestart = mb_strpos($pageTmpl,'<_vue>');
		$vueend = mb_strpos($pageTmpl,'</_vue>');

		if ($vuestart !== false) {
			$pageInfo['_vue'] = mb_substr($pageTmpl,$vuestart+6,$vueend-$vuestart-6);
		} else {
			$pageInfo['_vue'] = '{el: "#panel",components : window.components,data : gdata}';
		};

		if (!$pageInfo['template']) {$pageInfo['template'] == "default";};

		if (isset($_POST['get-page-data'])) { //Если запрашиваются данные страницы
			adminPanelGetData();
		} else {
			if (is_file($templatesPath.$pageInfo['template']."/index.php")) {
				$tempLink = $templatesLink.$pageInfo['template']."/";
				require_once($templatesPath.$pageInfo['template']."/index.php");
			} else {
				echo "<meta charset='UTF-8'><h1>Несуществующий шаблон страницы</h1>";
				exit;
			};
		}
	} else {
		echo "<meta charset='UTF-8'><h1>Нет такой страницы, 404</h1>";
		exit;
	};

?>