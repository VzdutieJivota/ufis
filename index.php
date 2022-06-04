<?php
require_once('api.php');
$api = new API();

if(isset($_GET['act'])){
    switch($_GET['act']){
        case 'auth':
            $api->auth();
            break;
        case 'logout': 
            $api->logout();
            break;
        case 'changeStatus':
            $api->changeStatus();
            break;
    }
}
?>
<html>
    <head>
        <meta charset="utf-8"/>
        <Title>5 вариант</Title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php $api->getError(); ?>
        <div class="container">
            <?php if(!$api->isAuthorized()): ?>
                <div class="content"><form action="/ufis/index.php" method="GET">
                    <input type="hidden" name="act" value="auth"/>
                    <input class="input_login" type="password" name="pass" placeholder="введите пароль"/>
                    <button class="input_login" type="submit">Войти</button>
                </form></div>
            <?php else: ?>
                <?php $api->getOrdersTable(); ?>
            <?php endif; ?>
        </div>
        <?php $api->getFooter(); ?>
    </body>
</html>

