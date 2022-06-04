<?php
session_start();
class API{
    private $pass = '12345';
    private $db;
    public $error = null;
    function __construct(){
        $this->db = mysqli_connect("localhost", "user", "", "ufis");
    }
    public function isAuthorized(){
        return isset($_SESSION['auth']);
    }
    public function logout(){
        unset($_SESSION['auth']);
        header('Location: /ufis');
    }
    public function auth(){
        if(!isset($_GET['pass'])) return;
        if($_GET['pass'] == $this->pass) $_SESSION['auth'] = true;
        else $this->error = 'Неверный пароль';
    }
    public function delProduct($id){
        $this->db->query('DELETE FROM `products` WHERE `id`='.intval($id));
    }
    public function addProduct(){
        if(!isset($_POST['name']) || !isset($_POST['description']) || !isset($_POST['cost'])){
            $this->error = 'Не все поля заполнены';
        }
        $name = $this->db->real_escape_string($_POST['name']);
        $desc = $this->db->real_escape_string($_POST['description']);
        $cost = intval($_POST['cost']);
        $delivery = isset($_POST['delivery']);
        $this->db->query('INSERT INTO `products` (`name`,`description`,`cost`,`delivery`) VALUES ("'.$name.'","'.$desc.'",'.$cost.','.intval($delivery).')');
        header('Location: /ufis/products.php');
    }
   
    public function getError(){
        if($this->error != null){
            echo '<font color="red">'.$this->error.'</font>';
        }
    }
    public function getOrdersTable(){
        $products = $this->getProductsList();
        $sumAll = 0;
        $statusText = ['Не оплачен','Оплачен','Выполнен'];
        echo '<table width="90%"><thead>
        <tr>
            <th>Имя</th>
            <th>Адрес</th>
            <th>Телефон</th>
            <th>Товары</th>
            <th>Итог</th>
            <th>Дата</th>
            <th>Статус</th>
        </tr>
    </thead>
    <tbody>';
    $r = $this->db->query('SELECT * FROM `orders`');
    while($d = $r->fetch_assoc()){
        $pr = [];
        $sum = 0;
        foreach(json_decode($d['products'],true) as $id => $count){
            if(!isset($products[$id])) $pr[] = 'Удаленный товар ('.$count.'шт, 0₽)';
            else{
                $pr[] = $products[$id]['name'].' ('.$count.'шт, '.number_format($products[$id]['cost']*$count).'₽)';
                $sum += $products[$id]['cost']*$count;
                if($d['status'] != 0) $sumAll += $products[$id]['cost']*$count;
            } 
        }
        echo '<tr>
            <td>'.$d['name'].'</td>
            <td>'.$d['address'].'</td>
            <td>'.$d['phone'].'</td>
            <td>'.implode(', ',$pr).'</td>
            <td>'.number_format($sum).'₽</td>
            <td>'.gmdate('H:i d.m.Y',$d['ts']+7*3600).'</td>
            <td>'.$statusText[$d['status']].' -> <a href="/ufis/?act=changeStatus&id='.$d['id'].'">'.(isset($statusText[$d['status']+1]) ? $statusText[$d['status']+1] : 'удалить').'</a></td>
        </tr>';
    }
    echo '</tbody>
    <tfoot>
        <tr>
        <th></th>
        <th></th>
        <th></th>
            <th>Всего оплачено</th>
            <th>'.number_format($sumAll).'₽</th>
        </tr>
    </thead></table>';
    }
    public function getProductsList(){
        $pr = [];
        $r = $this->db->query('SELECT * FROM `products`');
        while($d = $r->fetch_assoc()){
            $pr[$d['id']] = $d;
        }
        return $pr;
    }
    public function changeStatus(){
        if(!isset($_GET['id'])){
            $this->error = 'Не указан ID заказа'; return;
        }
        $id = intval($_GET['id']);
        $d = $this->db->query('SELECT * FROM `orders` WHERE `id`='.$id)->fetch_assoc();
        if($d == null){
            $this->error = 'Заказ не найден'; return;
        }
        if($d['status'] == 2){
            $this->db->query('DELETE FROM `orders` WHERE `id`='.$id);
        }else{
            $this->db->query('UPDATE `orders` SET `status`=`status`+1 WHERE `id`='.$id);
        }
    }
    public function getProductsTable(){
        echo '<table width="90%"><thead>
            <tr>
                <th>Название</th>
                <th>Описание</th>
                <th>Цена</th>
                <th>Доставка</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>';
        $r = $this->db->query('SELECT * FROM `products`');
        while($d = $r->fetch_assoc()){
            echo '<tr>
            <td>'.$d['name'].'</td>
            <td>'.$d['description'].'</td>
            <td>'.number_format($d['cost']).'₽</td>
            <td>'.($d['delivery'] ? 'Да' : 'Нет').'</td>
            <td><button onClick="location.href = \'/ufis/products.php?act=delProduct&id='.$d['id'].'\'">x</button></td>
        </tr>';
        }
        echo '</tbody></table>';
    }
    public function addOrder(){
        var_dump($_POST);
        if(!isset($_POST['name']) || !isset($_POST['address']) || !isset($_POST['phone']) || !isset($_POST['products']) || !isset($_POST['pr_count'])){
            $this->error = 'Не все поля заполнены';
            return;
        }
        $name = $this->db->real_escape_string($_POST['name']);
        $address = $this->db->real_escape_string($_POST['address']);
        $phone = $this->db->real_escape_string($_POST['phone']);
        $products = [];
        foreach($_POST['products'] as $k => $id){
            if(isset($products[$id])) $products[$id] += intval($_POST['pr_count'][$k]);
            else $products[$id] = intval($_POST['pr_count'][$k]);
        }
        $this->db->query('INSERT INTO `orders` (name,address,phone,products,ts) VALUES ("'.$name.'","'.$address.'","'.$phone.'","'.addslashes(json_encode($products)).'",'.time().')');
        header('Location: /ufis');
    }
    public function getProductsSelect(){
        foreach($this->getProductsList() as $id => $data){
            echo '<option value="'.$id.'">'.$data['name'].'</option>';
        }
    }
    public function getFooter(){
        $navs = [
            '/ufis/index.php' => 'Заказы',
            '/ufis/addOrder.php' => 'Новый заказ',
            '/ufis/products.php' => 'Товары',
            '/ufis/addProduct.php' => 'Добавить товар'
        ];
        echo '<div style="height: 36px"></div><div class="footer">';
        foreach($navs as $url => $name){
            echo '<button '.($_SERVER['DOCUMENT_URI'] == $url ? 'disabled' : '').' onClick="location.href = \''.$url.'\'">'.$name.'</button>';
        }
        echo '<a href="https://github.com/VzdutieJivota/ufis">github</a>';
        if($this->isAuthorized()) echo '<button style="right: 10px; position: absolute;" onclick="location.href = \'/ufis/?act=logout\'">Выйти</button>';
        echo '</div>';
    }
    function __destruct(){
        $this->db->close();
    }
}