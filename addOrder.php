<?php
require_once('api.php');
$api = new API();
if(!$api->isAuthorized()) header('Location: /ufis');
if(isset($_POST['act'])){
    switch($_POST['act']){
        case 'addOrder':
            $api->addOrder();
            break;
    }
}
?>

<html>
    <head>
        <meta charset="utf-8"/>
        <Title>Новый заказ</Title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="style.css">
        <script type="text/javascript">
            let select = '<select name="products[]" id="select"><?php $api->getProductsSelect(); ?></select><input width="5" name="pr_count[]" value="1" type="number">шт</input><br/>';
            function addSelect(){
                document.getElementById('products').insertAdjacentHTML('beforeend',select);
            }
        </script>
    </head>
    <body onload="addSelect()">
    <?php $api->getError(); ?>
        <div class="container">
                <form action="/ufis/addOrder.php" method="POST">
                    <input class="input" type="hidden" name="act" value="addOrder"/><br/>
                    <input class="input" type="text" name="name" placeholder="Имя"/><br/>
                    <input class="input" type="text" name="address" placeholder="Адрес"/><br/>
                    <input class="input" type="text" name="phone" placeholder="Телефон"/><br/>
                    <label>Товары:</label><br/>
                    <div id="products"></div>
                    <button onclick="addSelect()" type="button">Добавить товар</button>
                    <button class="input" type="submit">Создать</button>
                </form>
        </div>
        <?php $api->getFooter(); ?>
    </body>
</html>