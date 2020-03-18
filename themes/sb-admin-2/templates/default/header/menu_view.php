<? require_once("menu_model.php"); ?>
      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/<?=$PARAMS->get("panel_link")?>">
        <div class="sidebar-brand-icon rotate-n-15">
          <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Cross Engine</div>
      </a>
      <hr class="sidebar-divider m-0">
      <?
      
      foreach ($menu as $i=>$item) {
        if (@$item['type'] == "hr") {
          echo '<hr class="sidebar-divider">';
          if ($item['title']) {
            echo '<div class="sidebar-heading">'.$item['title'].'</div>';
          };
        } else {
          if (!$item['subs']) {
            echo '<li class="nav-item"><a class="nav-link" href="/'.$link.$item['link'].'">';
            if ($item['icon']) {echo '<i class="fas fa-fw '.$item['icon'].'"></i>';};
            echo '<span>'.$item['title'].'</span></a></li>';
          } else {
            echo '<li class="nav-item">
                  <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#navCollapse'.$i.'" aria-expanded="true" aria-controls="navCollapse'.$i.'">';

            if ($item['icon']) {echo '<i class="fas fa-fw '.$item['icon'].'"></i>';};
            echo '<span>'.$item['title'].'</span></a>';
            echo '<div id="navCollapse'.$i.'" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar"><div class="bg-white py-2 collapse-inner rounded">';
              foreach ($item['subs'] as $s=>$sub) {
                echo '<a class="collapse-item" href="/'.$link.$sub['link'].'">'.$sub['title'].'</a>';
              };
            echo '</div></div></li>';
          }
        }
      }; 
      
      ?>
      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>