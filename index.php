<form action="" method="post" id="CRUD" method="post">
    <input type="text" name="id" size="20" value="ID" maxlength="25">
    <input type="text" name="name" size="20" value="Name" maxlength="25">
    <input type="text" name="price" size="20" value="Price" maxlength="25">
    <input type="text" name="description" size="20" value="Description" maxlength="25">
    <input type="text" name="characteristic" size="20" value="Characteristic" maxlength="25">
    <input type="submit" value="Создать" name="Insert">
    <input type="submit" value="Обновить"  name="Update">
    <input type="submit" value="Удалить" name="Delete">
</form>


<?php

class CRUD
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
            self::$instance = new CRUD();
        }
        return self::$instance;
    }

    private function __clone()
    {
    }

    public function select($query)//Вывод
    {
        $result = $this->link->query($query) or die($this->link->error . __LINE__);
        if ($result->num_rows > 0) {
            return $result;
        } else {
            return FALSE;
        }
    }
    public function insert($query){//Ввод
        $insert = $this->link->query($query) or die($this->link->error.__LINE__);
        if($insert){
            return $insert;
        }else{
            return FALSE;
        }
    }

    public function update($query){//Обновление
        $update = $this->link->query($query) or die($this->link->error.__LINE__);
        if($update){
            return $update;
        }else{
            return FALSE;
        }
    }
    
    public function delete($query){ //Удаление
        $delete = $this->link->query($query) or die($this->link->error.__LINE__);
        if($delete){
            return $delete;
        }else{
            return FALSE;
        }
    }
}

$sing = CRUD::getInstance();

if (isset($_POST['Insert'])){
    $sing->insert("INSERT INTO Product (Name, Price, Description, Characteristic) 
VALUES ('".$_POST['name']."','".$_POST['price']."','".$_POST['description']."','".$_POST['characteristic']."')");
}

if (isset($_POST['Update'])){
    $query = "UPDATE Product  SET";
    if($_POST['name']!= "Name"){
        $query = $query." Name = '".$_POST['name']."'";
    }
    if($_POST['price']!="Price"){
        $query= $query.", Price = '".$_POST['price']."'";
    }
    if($_POST['description']!="Description"){
        $query= $query.", Description = '".$_POST['description']."'";
    }
    if($_POST['characteristic']!="Characteristic"){
        $query= $query.", Characteristic = '".$_POST['characteristic']."'";
    }
    echo ($query." WHERE id = ".$_POST['id']);
    $sing->update($query." WHERE id = ".$_POST['id']);
}

if (isset($_POST['Delete'])) {
    $sing->delete("DELETE FROM Product WHERE id = ".$_POST['id']);
}



$result = $sing->select("SELECT * FROM Product");
echo '<table border = 1>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Price</th>
        <th>Description</th>
        <th>Characteristic</th>
    </tr>';
while($row = mysqli_fetch_array($result))
{
    echo '<tr>
                <th>' . $row[id] . '</th>
                <th>' . $row[Name] . '</th>
                <th>' . $row[Price] . '</th>
                <th>' . $row[Description] . '</th>
                <th>' . $row[Characteristic] . '</th>
            </tr>';
}
