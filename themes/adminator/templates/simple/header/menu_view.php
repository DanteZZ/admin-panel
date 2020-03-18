<? require_once("menu_model.php"); ?>

      <?
      $a = 0;
      foreach ($menu as $i=>$item) {
        if ($a == 0) {$cl = "mT-30"; $a=1;} else {$cl="";};
        if (@$item['type'] == "hr") {
          // Отображение hr
        } else {
          if (!$item['subs']) {
            echo '<li class="nav-item"><a class="sidebar-link" href="/'.$link.$item['link'].'">';
            if ($item['icon']) {echo '<span class="icon-holder"><i class="fas '.$item['icon'].'"></i></span>';};
            echo '<span class="title">'.$item['title'].'</span></a></li>';
          } else {

            echo '<li class="nav-item dropdown"><a class="dropdown-toggle" href="javascript:void(0);">';

            if ($item['icon']) {echo '<span class="icon-holder"><i class="fas '.$item['icon'].'"></i></span>';};

            echo '<span class="title">'.$item['title'].'</span><span class="arrow"><i class="ti-angle-right"></i></span></a>';

            echo '<ul class="dropdown-menu">';
              foreach ($item['subs'] as $s=>$sub) {
                echo '<li><a class="sidebar-link" href="/'.$link.$sub['link'].'">'.$sub['title'].'</a></li>';
              };
            echo '</ul></li>';

          }
        }
      }; 
      
      ?>