<?
  
  GLOBAL $is_dev, $PAGES, $MDL, $DS, $USR, $PARAMS, $EVENT, $MDL, $panelPath, $panelLink, $panelURL, $LNG, $se_multilocal, $login_error, $_PDATA;
  
  $panelLink = $MDL->path;
  $panelPath = $_SERVER['DOCUMENT_ROOT'].$panelLink;
  $panelURL  = "";
  $_PDATA = Array();

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
    GLOBAL $DS, $PARAMS, $USR, $_PDATA;
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

    if ($pageInfo['datajson']) {
      $data = json_decode($pageInfo['datajson'],true);
      if (!$data) {$data = Array();};
      unset($pageInfo['datajson']);
    };

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

    if ($pageInfo['dataphp']) {
      $_PDATA = $data;
      $phparr = eval('GLOBAL $_PDATA; '.$pageInfo['dataphp']);
      $data = $_PDATA;

      if ($phparr) {
        $data = array_merge($data,$phparr);
      };
      
      
      unset($pageInfo['dataphp']);
    };

    $data['PAGE'] = $pageInfo;
    unset($data['PAGE']['_template']);
    echo json_encode($data);
  };

  function adminPanelGen() {
    GLOBAL $components,$panelCFG,$templatesPath,$themeLink,$components,$pageInfo,$panelPath,$panelLink, $EVENT;
    
    $plugList = Array("jquery","tinymce","responsivefilemanager");

    /* ----- Проверяем модули ----- */
    $needleComps = $components;

      /* ----- Вывод CSS ----- */

      if ($pageInfo['style']) {echo "<style>".$pageInfo['style']."</style>";};
      
      /* ----- Вывод компонентов ----- */

      echo "<script src='".$panelLink."assets/tinymce/tinymce.min.js'></script>";
      echo "<script src='".$panelLink."assets/vue.js'></script>";
      echo "<script src='".$panelLink."assets/vue-loader.js'></script>";

      ?>
      <script src="https://rawgithub.com/ajaxorg/ace-builds/master/src/ace.js"></script>
      <script src="https://rawgithub.com/ajaxorg/ace-builds/master/src/ext-emmet.js">  </script>
      <script src="https://rawgithub.com/nightwing/emmet-core/master/emmet.js"></script>
      <?


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
        echo $pageInfo['_template'];
      echo "</div>";
      echo "<script src='".$panelLink."assets/panel.js'></script>";
      ?>
        <script>
          $(document).ready(function(){
            $.ajax({
              method:"post",
              data: {"get-page-data":true},
              dataType: "json",
              success: function(gdata){
                console.log(gdata);
                if (typeof(gdata) !== "object") {
                  gdata = {};
                };
                window.panel = new Vue(<?=$pageInfo["_vue"]?>);
              }
            });
          });

        </script>
      <?
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