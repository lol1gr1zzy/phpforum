<?php
    $db = mysqli_connect ("phpforum","root","","forum");
?>
<?php
if(empty($_POST)){
    header('Location: /index.php');
    exit();
}

$result = $_POST['note'];
if(empty($result)or $result == null){
    header('Location: index.php?msg=Поле должно быть заполнено');
    exit();

}
$len = strlen($result);
if ($len < 3 or $len > 120){
    $err_message = urldecode('Текст должен содержать не менее 4 и не более 120 символов');
    header('Location: index.php?err_message='.$err_message);
    exit();
}
$file = $_FILES['image']['name'];
$tmp_name = $_FILES['image']['tmp_name'];
$pathFile = __DIR__.'/uploads/'. $file;
move_uploaded_file($tmp_name, "uploads/" . $file);
date_default_timezone_set('Europe/Moscow');
var_dump($pathFile);
$time = date("d-m-y H:i");
$id = 0;
$result2 = mysqli_query ($db,"INSERT INTO posts (id, text, time, image) VALUES('$id', '$result', '$time', '$file')");
header('Location: /index.php');
exit();
?>