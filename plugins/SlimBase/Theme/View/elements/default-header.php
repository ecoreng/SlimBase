<?php
// Compile Less on load
// Remove for production
require "../plugins/{$theme}/public/css/lessc.inc.php";
$less = new lessc;
$less->checkedCompile("../plugins/{$theme}/public/css/style.less", "../plugins/{$theme}/public/css/style.css");

if (!isset($template)) {
    $template = 'default';
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo $pageTitle; ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, user-scalable=no">
        <link rel="stylesheet" href="<?php echo $pluginsBaseUrl . $theme; ?>/css/normalize.min.css">
        <link rel="stylesheet" href="<?php echo $pluginsBaseUrl . $theme; ?>/css/main.css">
        <link rel="stylesheet" href="<?php echo $pluginsBaseUrl . $theme; ?>/css/style.css">

        <?php echo $pageStylesheets; ?>

        <!--[if lt IE 9]>
            <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
            <script>window.html5 || document.write('<script src="<?php echo $pluginsBaseUrl . $theme;
        ; ?>/js/vendor/html5shiv.js"><\/script>')</script>
        <![endif]-->
    </head>
    <body class='template-<?php echo $template; ?> layout-<?php echo $layout; ?>'>
