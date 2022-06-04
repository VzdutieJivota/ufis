<?php
require_once('api.php');
$api = new API();
if(!$api->isAuthorized()) header('Location: /ufis');
if(isset($_GET['act']) && isset($_GET['id']) && $_GET['act'] == 'delProduct'){
    $api->delProduct($_GET['id']);
}
?>
<html>
    <head>
        <meta charset="utf-8"/>
        <Title>Товары</Title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php $api->getError(); ?>
        <div class="container">
           <?php $api->getProductsTable(); ?>
        </div>
        <?php $api->getFooter(); ?>
    </body>
</html>