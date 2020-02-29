<?php

class Header {
/**
 * @param $page Page
 */
function __construct($page) {
    $this->page = $page;
    if ($page->settings->header_show_totals) {
        $t = $page->settings->table;
        $t_bans = $t['bans'];
        $t_mutes = $t['mutes'];
        $t_warnings = $t['warnings'];
        $t_kicks = $t['kicks'];
        try {
            $st = $page->conn->query("SELECT
            (SELECT COUNT(*) FROM $t_bans),
            (SELECT COUNT(*) FROM $t_mutes),
            (SELECT COUNT(*) FROM $t_warnings),
            (SELECT COUNT(*) FROM $t_kicks)");
            ($row = $st->fetch(PDO::FETCH_NUM)) or die('Failed to fetch row counts.');
            $st->closeCursor();
            $this->count = array(
                'bans.php'     => $row[0],
                'mutes.php'    => $row[1],
                'warnings.php' => $row[2],
                'kicks.php'    => $row[3],
            );
        } catch (PDOException $ex) {
            Settings::handle_error($page->settings, $ex);
        }
    }
}

function navbar($links) {
    echo '<ul class="navbar-nav mr-auto">';
    foreach ($links as $page => $title) {
        $li = "li";
        $class = "nav-item";
        if ((substr($_SERVER['SCRIPT_NAME'], -strlen($page))) === $page) {
            $class .= " active navbar-active";
        }
        $li .= " class=\"$class\"";

        if ($this->page->settings->header_show_totals && isset($this->count[$page])) {
            $title .= ' <span class="' . $this->page->settings->badge_classes . '">';
            $title .= $this->count[$page];
            $title .= "</span>";
        }
        echo "<$li><a class=\"nav-link\" href=\"$page\">$title</a></li>";
    }
    echo '</ul>';
}

function print_header($admin_header = false) {
$settings = $this->page->settings;

$dir = __DIR__ . '/../..';
require($dir . '/src/XF.php');

XF::start($dir);
$app = XF::setupApp('XF\Pub\App');
$app->start();
$visitor = \XF::visitor();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="LiteBans">
    <link href="<?php echo $this->page->autoversion('/inc/img/favicon.ico'); ?>" rel="shortcut icon">
    <!-- CSS -->
    <link href="<?php echo $this->page->autoversion('/inc/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo $this->page->autoversion('/inc/css/glyphicons.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo $this->page->autoversion('/inc/css/custom.css'); ?>" rel="stylesheet">
    <script type="text/javascript">
        function withjQuery(tries, f) {
            if (window.jQuery) f();
            else if (tries > 0) window.setTimeout(function () {
                withjQuery(tries - 1, f);
            }, 100);
        }
    </script>
</head>


<header role="banner">
    <div class="navbar navbar-expand-lg fixed-top <?php echo $settings->navbar_classes; ?>">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $settings->name_link; ?>">
                <?php
                  echo $settings->name;
                  if ($admin_header) { echo "<small>Staff</small>"; }
                ?>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#litebans-navbar"
                    aria-controls="litebans-navbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="litebans-navbar">
                <?php
                  if ($admin_header) {
                    $navbarOptions = array(
                      "index.php" => $this->page->t('title.index'),
                      "users.php" => $this->page->t('title.admin.all_users'),
                      "punish.php" => $this->page->t('title.admin.punish_user'),
                      "stats.php" => $this->page->t('title.admin.stats')

                    );
                  } else {
                    $navbarOptions = array(
                        "index.php"    => $this->page->t("title.index"),
                        "bans.php"     => $this->page->t("title.bans"),
                        "mutes.php"    => $this->page->t("title.mutes"),
                        "warnings.php" => $this->page->t("title.warnings"),
                        "kicks.php"    => $this->page->t("title.kicks"),
                    );
                  }

                  if ($visitor->is_staff && !$admin_header) {
                    $navbarOptions['admin/index.php'] = '<font style="color: #e74c3c">' . $this->page->t('title.admin') . '</font>';
                  }

                  $this->navbar($navbarOptions);

                  if (!$visitor->user_id) {
                ?>
                <span>
                  <a href="/index.php?login" id="login" role="button" class="btn auth btn-primary">Login</a>
                </span>
                <?php
                  } else {
                ?>
                <span>
                  <p class="auth"> Logged in as <? echo $visitor->username ?> </p>
                </span>
                <?php } ?>
            </div>
        </div>
</header>

<br><br><br>
<?php
}
}
?>
