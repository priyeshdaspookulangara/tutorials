<?php
require_once __DIR__ . '/language.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang_to_use; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo trans('app_title'); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="/"><?php echo trans('app_title'); ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="/"><?php echo trans('nav_home'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/tutorials"><?php echo trans('nav_tutorials'); ?></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownLang" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo strtoupper($lang_to_use); ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownLang">
                    <?php foreach ($available_langs as $lang_code): ?>
                        <?php if ($lang_code !== $lang_to_use): ?>
                            <a class="dropdown-item" href="?lang=<?php echo $lang_code; ?>"><?php echo strtoupper($lang_code); ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/user/dashboard.php"><?php echo trans('nav_dashboard'); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/user/logout.php"><?php echo trans('nav_logout'); ?></a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" in_id="login" href="/user/login.php"><?php echo trans('nav_login'); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/user/register.php"><?php echo trans('nav_register'); ?></a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<div class="container mt-4">
