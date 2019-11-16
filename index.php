<form action="" method="post" id="CRUD" method="post">
    <p>ID:<input type="text" name="id" size="20" maxlength="25"></p>
    <p>Наименование:<input type="text" name="name" size="20" value="" maxlength="25"></p>
    <p>Ценна:<input type="text" name="price" size="20" value="" maxlength="25"></p>
    <p>Описание:<input type="text" name="description" size="20" value="" maxlength="25"></p>
    <p>Характеристика:<input type="text" name="characteristic" size="20" value="" maxlength="25"></p>
    <input type="submit" value="Создать" name="Insert">
    <input type="submit" value="Обновить"  name="Update">
    <input type="submit" value="Удалить" name="Delete">
</form>

<?php
class BD
{
    public $link;
    public $error;
    private static $instance = null;

    private function __construct()//Подключение к БД
    {
        $this->link = new mysqli('localhost', 'root', '123', 'testDB');
        if (!$this->link) {
            $this->error = "Database error" . $this->link->connect_error;
            return FALSE;
        }
    }

    public static function getInstance()//Единожды запускаем подключение
    {
        if (is_null(self::$instance)) {
            self::$instance = new BD();
        }
        return self::$instance;
    }

    private function __clone()
    {
    }

    public function zapros($query)//Запрос к БД
    {
        $result = $this->link->query($query) or die($this->link->error . __LINE__);
        if ($result->num_rows > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }
}

$sing = BD::getInstance();
//Добавление новой информации
if (isset($_POST['Insert'])){
    if($_POST['name'] && $_POST['price'] && $_POST['description'] && $_POST['characteristic']) {
        $sing->zapros("Start transaction");
        $sing->zapros("INSERT INTO Product (Name, Description, Characteristic) 
                    VALUES ('" . $_POST['name'] . "','" . $_POST['description'] . "','" . $_POST['characteristic'] . "')");
        $sing->zapros("INSERT INTO price (id_product,price)
                    VALUES(LAST_INSERT_ID(),'" . $_POST['price'] . "')");
        $sing->zapros("commit");
    }
    else{
        echo "Заполнте поля: Наименование,Цена,Описание,Характеристика";
    }
}
//обновление информации
if (isset($_POST['Update'])){
    if($_POST['id']){
        if($_POST['name'] || $_POST['price'] || $_POST['description'] || $_POST['characteristic']) {
            $sing->zapros("Start transaction");
            if ($_POST['name']) {
                $sing->zapros("UPDATE Product SET Name = '" . $_POST['name'] . "' WHERE id = '" . $_POST['id'] . "'");
            }
            if ($_POST['description']) {
                $sing->zapros("UPDATE Product SET description = '" . $_POST['description'] . "' WHERE id = '" . $_POST['id'] . "'");
            }
            if ($_POST['characteristic']) {
                $sing->zapros("UPDATE Product SET characteristic = '" . $_POST['characteristic'] . "' WHERE id = '" . $_POST['id'] . "'");
            }
            if ($_POST['price']) {
                $sing->zapros("UPDATE price SET price = '" . $_POST['price'] . "' WHERE id_product = '" . $_POST['id'] . "'");
            }
            $sing->zapros("commit");
        }
        else{
            echo "Укажите один или несколько параметров для изменения";
        }
    }
    else
    {
            echo "Нужно ввести ID изменяемого продукта, а также параметр который хотите поменять";
    }
}

if (isset($_POST['Delete'])) {//удаление продукта
    if($_POST['id']) {
        $sing->zapros("Start transaction");
        $sing->zapros("DELETE FROM Product WHERE id = " . $_POST['id']);
        $sing->zapros("DELETE FROM price WHERE id_product = " . $_POST['id']);
        $sing->zapros("commit");
    }
    else{
        echo "Укажите ID товара который нужно удалить";
    }
}

//вывод таблицы целликом
$result = $sing->zapros("SELECT * FROM Product left join price on price.id_product=Product.id");
echo '<table border = 1>
    <tr>
        <th>ID</th>
        <th>Наименование</th>
        <th>Цена</th>
        <th>Описание</th>
        <th>Характеристика</th>
    </tr>';
while($row = mysqli_fetch_array($result))
{
    echo '<tr>
                <th>' . $row[id] . '</th>
                <th>' . $row[Name] . '</th>
                <th>' . $row[price] . '</th>
                <th>' . $row[description] . '</th>
                <th>' . $row[characteristic] . '</th>
            </tr>';
}
