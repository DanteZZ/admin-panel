<?

  GLOBAL $themeLink, $USR, $LNG, $se_multilocal, $se_locals;
  require_once("header_model.php");

?>

<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <title>Панель управления</title>
    <style>
        #loader {
            transition: all .3s ease-in-out;
            opacity: 1;
            visibility: visible;
            position: fixed;
            height: 100vh;
            width: 100%;
            background: #fff;
            z-index: 90000
        }
        
        #loader.fadeOut {
            opacity: 0;
            visibility: hidden
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            position: absolute;
            top: calc(50% - 20px);
            left: calc(50% - 20px);
            background-color: #333;
            border-radius: 100%;
            -webkit-animation: sk-scaleout 1s infinite ease-in-out;
            animation: sk-scaleout 1s infinite ease-in-out
        }
        
        @-webkit-keyframes sk-scaleout {
            0% {
                -webkit-transform: scale(0)
            }
            100% {
                -webkit-transform: scale(1);
                opacity: 0
            }
        }
        
        @keyframes sk-scaleout {
            0% {
                -webkit-transform: scale(0);
                transform: scale(0)
            }
            100% {
                -webkit-transform: scale(1);
                transform: scale(1);
                opacity: 0
            }
        }
    </style>
    <script src="<?=$themeLink?>assets/jquery/jquery-3.4.1.min.js"></script>
</head>

<body class="app">
    <div id="loader">
        <div class="spinner"></div>
    </div>
    <script>
        window.addEventListener('load', function load() {
            const loader = document.getElementById('loader');
            setTimeout(function() {
                loader.classList.add('fadeOut');
            }, 300);
        });
    </script>
    <div>
        <div class="sidebar">
            <div class="sidebar-inner">
                <div class="sidebar-logo">
                    <div class="peers ai-c fxw-nw">
                        <div class="peer peer-greed">
                            <a class="sidebar-link td-n" href="/<?=$PARAMS->get("panel_link")?>">
                                <div class="peers ai-c fxw-nw">
                                    <div class="peer">
                                        <div class="logo"><img src="<?=$themeLink?>assets/static/images/logo.png" alt=""></div>
                                    </div>
                                    <div class="peer peer-greed">
                                        <h5 class="lh-1 mB-0 logo-text">Cross Engine</h5></div>
                                </div>
                            </a>
                        </div>
                        <div class="peer">
                            <div class="mobile-toggle sidebar-toggle"><a href="" class="td-n"><i class="far fa-arrow-alt-circle-left"></i></a></div>
                        </div>
                    </div>
                </div>
                <ul class="sidebar-menu scrollable pos-r">
                    <? require_once("menu_view.php"); ?>
                </ul>
            </div>
        </div>
        <div class="page-container">
            <div class="header navbar">
                <div class="header-container">
                    <ul class="nav-left">
                        <li><a id="sidebar-toggle" class="sidebar-toggle" href="javascript:void(0);"><i class="fas fa-bars"></i></a></li>
                    </ul>
                    <ul class="nav-right">
                        
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

                        <li class="dropdown">
                            <a href="" class="dropdown-toggle no-after peers fxw-nw ai-c lh-1" data-toggle="dropdown">
                                <div class="peer mR-10"><img class="w-2r bdrs-50p" src="/dev/img/user.png" alt=""></div>
                                <div class="peer"><span class="fsz-sm c-grey-900"><? echo $USR->getParameter("username"); ?></span></div>
                            </a>
                            <ul class="dropdown-menu fsz-sm">
                                <li>
                                  <a href="?logout" class="d-b td-n pY-5 bgcH-grey-100 c-grey-700">
                                    <i class="ti-power-off mR-10"></i> <span>Выход</span></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>