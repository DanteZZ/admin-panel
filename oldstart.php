<?
  
  GLOBAL $is_dev, $PAGES, $MDL, $DS, $USR, $PARAMS, $EVENT, $MDL, $panelPath, $panelLink, $panelURL, $LNG, $se_multilocal, $login_error;
  
  $panelLink = $MDL->path;
  $panelPath = $_SERVER['DOCUMENT_ROOT'].$panelLink;
  $panelURL  = "";


  GLOBAL $DB;

 

  if (!$PARAMS->is("panel_link")) {
    $PARAMS->set("panel_link","panel");
  };

  if (isset($_POST['set_panel_locale'])) {
  	$_SESSION['panel_locale'] = $_POST['set_panel_locale'];
  };

  if ($se_multilocal) {
  	if ($_SESSION['panel_locale']) {
  		$LNG->locale = $_SESSION['panel_locale'];
  	};
  };

  if (isset($_GET['logout'])) {
    $USR->logout();
  };

  /*
	if (@$is_dev) {
	    @$PAGES->regPage("paneleditor",Array(
    	  "type"=>"simple",
          "title"=>"API",
          "path"=>$MDL->path."devpages/editor.php",
          "menu_show"=>true,
          "menu_icon"=>"fas fa-plug"
    	));
	};
  */

  function adminGetConfig() {
    GLOBAL $panelPath;
    if (is_file($panelPath."configs/config.json")) {
      return json_decode(file_get_contents($panelPath."configs/config.json"),true);
    } else {
      return false;
    };
  };

  function adminGetMenu() {
    GLOBAL $panelPath;
    if (is_file($panelPath."configs/menu.json")) {
      return json_decode(file_get_contents($panelPath."configs/menu.json"),true);
    } else {
      return false;
    };
  }

  function adminPanelGetData() {
    GLOBAL $components,$panelCFG,$templatesPath,$themeLink,$components,$pageInfo,$panelPath,$panelLink, $EVENT;
    GLOBAL $DS, $PARAMS, $USR;
    $rules = Array();

    foreach (array_diff(scandir($panelPath."data-rules"), array('.', '..')) as $rkey=>$file) {
      $rules[str_replace(".php", "", $file)] = $panelPath."data-rules/".$file;
    };

    //Проверка сторонних правил
    $thirds = $EVENT->init("panel_init",true);

    if ($thirds) {
      foreach ($thirds as $key=>$thr) { //Перебираем все сторонние данные
        if ($thr['data-rules']) { //Перебираем компоненты
          foreach (array_diff(scandir($_SERVER['DOCUMENT_ROOT']."/".$thr['data-rules']), array('.', '..')) as $rkey=>$file) {
            $rules[str_replace(".php", "", $file)] = $_SERVER['DOCUMENT_ROOT'].$thr['data-rules']."/".$file;
          };
        };
      }
    };

    foreach ($rules as $name => $link) {
      require_once($link);
    };

    $data = Array();

    if ($pageInfo["data"]) {
      foreach ($pageInfo['data'] as $dname=>$block) {
        if ($rules[$block['type']]) {
          $class = "DataRule_".$block['type'];
          $COMP = new $class;
          $data[$dname] = $COMP->compute($block['data'],$data);
        } else {
          $data[$dname] = null;
        };
      };
    };

    echo json_encode($data);
  };

  function adminPanelGen() {
    GLOBAL $components,$panelCFG,$templatesPath,$themeLink,$components,$pageInfo,$panelPath,$panelLink, $EVENT;
    
    $plugList = Array("jquery","tinymce","responsivefilemanager");

    $needleComps = Array();

    // ------ Перебор необходимых компонентов ------ //

    if ($pageInfo["sections"]) {
      $page = "";
      foreach ($pageInfo['sections'] as $sk => $section) {
        if (!@$section['type']) {$section['type'] == "simple";};

        switch ($section['type']) {
          case "card": 
          $page.='<div class="'.$section['class'].'"><div class="bgc-white bd bdrs-3 p-20 mB-20"><h4 class="c-grey-900 mB-20">'.$section['title'].'</h4>'; 
          break;

          case "card-form": $page.='<div class="'.$section['class'].'"><div class="bgc-white bd bdrs-3 p-20 mB-20"><h4 class="c-grey-900 mB-20">'.$section['title'].'</h4><div class="form-row">'; break;

          case "simple" : $page.='<h4 class="c-grey-900 mB-20">'.$section['title'].'</h4>'."<div class='".$section['class']."'>"; break;
        };

        if (!$section['components']) {continue;};
        $pcomps = $section['components'];
        foreach ($pcomps as $key=>$pcomp) {
          if ($components[$pcomp['component']]) {

            if (!in_array($pcomp['component'],$needleComps)) {$needleComps[] = $components[$pcomp['component']];};

            $props = " ";
            if ($pcomp['props']) { //Добавляем v-bind
              foreach ($pcomp['props'] as $pname=>$pval) {$props.=":$pname=\"$pval\" ";};
            };
            
            if ($pcomp['model']) { //Добавляем v-model
              $props.="v-model='".$pcomp['model']."' ";
            };

            if ($pcomp['if']) { //Добавляем v-model
              $props.="v-if='".$pcomp['if']."' ";
            };

            $page.="<".$pcomp['component'].$props."></".$pcomp['component'].">\n";
          } else {
            echo "<meta charset='UTF-8'><p><i>Несуществующий компонент <b>".$pcomp['component']."</b></i></p>";
          };
        };
        

        switch ($section['type']) {
          case "card-form": $page.='</div></div></div>'; break;
          case "card": $page.='</div></div>'; break;
          case "simple" : $page.="</div>"; break;
        };

      };
      
      /* ----- Вывод компонентов ----- */

      echo "<script src='".$panelLink."assets/tinymce/tinymce.min.js'></script>";
      echo "<script src='".$panelLink."assets/vue.js'></script>";
      echo "<script src='".$panelLink."assets/vue-loader.js'></script>";


      /* ----- Подключение плагинов ------ */
      $list = $EVENT->init("panel_load_plugins",true);
      if ($list) {
        foreach ($list as $key=>$mod) {
          foreach ($mod as $plug =>$inf) {
            if (!in_array($plug, $plugList)) { //Если такой плагин ещё не подключен
              
              if (gettype($inf['scripts']) == 'string') {$inf['scripts'] = Array($inf['scripts']);};
              if (gettype($inf['styles']) == "string") {$inf['styles'] = Array($inf['styles']);};
              
              if ($inf['scripts']) {
                foreach ($inf['scripts'] as $skey=>$link) {
                  echo "<script src='$link'></script>";
                };
              };
              if ($inf['styles']) {
                foreach ($inf['styles'] as $skey=>$link) {
                  echo "<link rel='stylesheet' href='$link'>";
                };
              };
              $plugList[] = $plug;
            };
          };
        };
      };

      if ($needleComps) {
        echo "<script> window.components = {";
        foreach ($needleComps as $key=>$comp) {
          echo "'".$comp['name']."' : window.httpVueLoader('".$comp['source']."'),\n";
        };
        echo "};</script>";
      };

      ?><div id="panel" class="row"><?
        echo $page;
      echo "</div>";
      echo "<script src='".$panelLink."assets/panel.js'></script>";
    };
  };

  function adminPanel_start() {
    GLOBAL $PARAMS, $panelPath,$panelURL,$panelLink, $contrAct;

    $part = str_replace("?".$_SERVER['QUERY_STRING'], "", $_SERVER['REQUEST_URI']);
    $uri = substr($part, 1);
    if (substr($uri,-1,1) == "/") {$uri = substr($uri,0,strlen($uri)-1);};
    $uriAr = @explode("/",$uri); //Массив ссылки
    if (isset($uriAr[0])) {
      if ($uriAr[0] ==  $PARAMS->get("panel_link")) {
        array_shift($uriAr);
        $panelURL = $uriAr;
        require_once($panelPath."router.php");
        return true;
      } else if ($uriAr[0] ==  "panel-handler"){
        $action = $uriAr[1];
        require_once($panelPath."handler.php");
        return true;
      };
      return false;
    };
  };

  $EVENT->set("stop-load","adminPanel_start()");
?>