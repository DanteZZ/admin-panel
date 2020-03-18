<?

  GLOBAL $themeLink, $USR, $LNG, $se_multilocal, $se_locals;
  require_once("header_model.php");

?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Панель управления</title>

  <!-- Custom fonts for this template-->
  <link href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="<?=$themeLink?>assets/css/sb-admin-2.min.css" rel="stylesheet">

  <script src="<?=$themeLink?>assets/vendor/jquery/jquery.min.js"></script>

</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
      <? require_once("menu_view.php"); ?>
    </ul>
    <!-- End of Sidebar -->
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>



          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">
            <? if ($se_multilocal) { ?>

              <div class="topbar-divider d-none d-sm-block"></div>
              <li class="nav-item pt-4 mr-2">
                <b>Локаль сайта</b>
              </li>
              <li class="nav-item">
                <div class="form-group mt-3">
                  <select class="form-control" id="select_panel_locale">
                    <?
                      foreach ($se_locals as $key=>$locale) {
                        $check = "";
                        if ($locale == $LNG->locale) { $check = "selected"; };
                        echo "<option value='$locale' $check >$locale</option>";
                      };
                    ?>
                  </select>
                </div>
              </li>

            <? }; ?>

            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><? echo $USR->getParameter("username"); ?></span>
                <img class="img-profile rounded-circle" src="/dev/img/user.png">
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Выйти
                </a>
              </div>
            </li>

          </ul>

        </nav>
        <!-- End of Topbar -->