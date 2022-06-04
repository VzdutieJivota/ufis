<?php
require_once('api.php');
$api = new API();
if(!$api->isAuthorized()) header('Location: /ufis');
if(isset($_POST['act'])){
    switch($_POST['act']){
        case 'addProduct':
            $api->addProduct();
            break;
    }
}
?>

<html>
    <head>
        <meta charset="utf-8"/>
        <Title>Добавить товар</Title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <?php $api->getError(); ?>
        <div class="container">
                <form action="/ufis/addProduct.php" method="POST">
                    <input class="input" type="hidden" name="act" value="addProduct"/><br/>
                    <input class="input" type="text" name="name" placeholder="Название товара"/><br/>
                    <input class="input" type="number" name="cost" placeholder="Цена"/>₽<br/>
                    <textarea class="input" placeholder="Описание" name="description"></textarea><br/>
                    <input type="checkbox" value="1" name="delivery" id="delivery">
                    <label for="delivery">Возможость доставки</label><br/>
                    <button class="input" type="submit">Добавить</button>
                </form>
        </div>
        <?php $api->getFooter(); ?>
    </body>
</html>