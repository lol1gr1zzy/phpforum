# Лабораторная работа N2

## Основное задание

Спроектировать и разработать систему для анонимного общения в сети
интернет.
Интерфейс системы должен представлять собой веб-страницу с лентой
заметок, отсортированных в обратном хронологическом порядке и форму
добавления новой заметки. В ленте отображается последние 100 заметок.
Возможности:

- Добавление текстовых заметок в общую ленту
- Реагирование на чужие заметки (лайки)
- Добавление комментариев к чужим заметкам
- Добавление изображений к заметкам
- Реакции разных видов
- Пагинация

## Стек
 -php 7.2
 -MySQL 8.0
 -Open Server

**1. Создание пользовательского интерфейса и описание пользовательских
сценариев работы**

![](https://github.com/lol1gr1zzy/phpforum/blob/main/png1.jpg)

```
1. На сайте пользователю доступны следующие возможности:
2. Добавление текстовых заметок в общую ленту
3. Реагирование на чужие заметки (лайк, дизлайк)
4. Добавление комментариев
5. Добавление изображений к заметкам
6. При создании новой заметки пользователь может создать ее как с
фотографией, так и без нее.
```
**2. Описание API сервера и хореографии**
    Добавление новой заметки:
    
![](https://github.com/lol1gr1zzy/phpforum/blob/main/png2.jpg)

Post запрос содержит следующие данные: текст поста.
Добавление лайка/дизлайка:

![](https://github.com/lol1gr1zzy/phpforum/blob/main/png3.jpg)

Post запрос содержит следующие данные:id записи, которую оценивают.
Добавление комментария:

![](https://github.com/lol1gr1zzy/phpforum/blob/main/png4.jpg)

Post запрос содержит следующие данные: текст комментария, id записи, которую
комментируют.

**3. Описание структуры базы данных**
    В качестве базы данных используется MySQL. Имеются 2 таблицы.
    В первой (posts) хранятся посты, время, их фото и количество лайков с
    дизлайками. Модель описана следующим образом:
    
![](https://github.com/lol1gr1zzy/phpforum/blob/main/DB1.jpg)
    
Комментарии хранятся в отдельной таблице. Каждая запись хранит в себе id
поста, с которым он связан и содержание комментария. Модель описана следующим
образом:

![](https://github.com/lol1gr1zzy/phpforum/blob/main/DB2.jpg)
    
**4. Описание алгоритмов**
Алгоритм действий пользователя
Создание нового поста:

![](https://github.com/lol1gr1zzy/phpforum/blob/main/alg1.jpg)

Пользователь оставляет комментарий:

![](https://github.com/lol1gr1zzy/phpforum/blob/main/alg2.jpg)

Пользователь ставит лайк/дизлайк:

![](https://github.com/lol1gr1zzy/phpforum/blob/main/alg3.jpg)

5.Значимые фрагменты кода:
Вывод постов на главную страницу:
```php
if($result = mysqli_query($db, $sql)){
    $page_count = ceil($rowsCount / $count);
    echo "<p></p>";

    foreach($result as $row){

        echo "<div><tr>";
            echo "<td>" . $row["text"] . "</td>: ";
            echo "<td>" . $row["time"] . "</td><br>"; 
            print "<img src='/uploads/{$row['image']}' width='200px'><br>";
            echo "<form action='like.php' method='POST'>
     <button style='width:25x;height:25px' name='like' type='submit'value='{$row['id']}'> 👍 {$row['likes']}</button>
     <button style='width:25x;height:25px' name='dislike' type='submit' value='{$row['id']}'> 👎 {$row['dislikes']}</button></form>";
            echo "<form action='comment.php' method'GET'>
                 <input name='comment' type='text' placeholder='Ваш коммент'><button name='com' type='submit' value='{$row['id']}'> Комм </button></form>";
            echo "<button id='btn <?php echo {$row['id']};?>' onclick='show_comments('<?php echo {$row['id']}')'>Развернуть</button>";
        echo "</tr><br></div>";
        if($result2 = mysqli_query($db, $sql2)){
            foreach($result2 as $com){
                if($com["post_id"] == $row["id"]){
                    echo "<pre><table id='c'><tr>";
                echo "<td>" . $com["text"] . "</td>";
                echo "<td>" . $com["time"] . "</td>";
                echo "</tr><br></table></pre>";
                }
            }
        }
        echo "<br><br><br><br>";
    }

    echo "</table>";
    //mysqli_free_result($result0);
    mysqli_free_result($result);
    //mysqli_free_result($result2);
} else{
    echo "Ошибка: " . mysqli_error($db);
}
```

Реализация пагинации:
```php
<?php
$sql0 = "SELECT `likes`, `dislikes`, `id`, `text`, `time`, `image` FROM posts";
$result0 = mysqli_query($db, $sql0);
$count = 2;
$rowsCount = mysqli_num_rows($result0); // количество полученных строк
if (isset($_GET['page'])){
    $page = $_GET['page'];
}
else{
    $page = 0;
}
$start_from = $page * 2;
$sql = "SELECT `likes`, `dislikes`, `id`, `text`, `time`, `image` FROM posts ORDER BY `time` DESC LIMIT $start_from, $count";
$sql2 = "SELECT `post_id`, `id`, `text`, `time` FROM comments ORDER BY `time` DESC";
```
```php
<?php for ($p = 0; $p < $page_count; $p++) :?>
<a href="?page=<?php echo $p; ?>"><button><?php echo $p + 1; ?></button></a>
<?php endfor; ?>
```
Создание новой записи в БД:
```php
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
```

Добавление реакции:
```php
<?php
if(isset($_POST['like'])){
    $id_like = $_POST['like'];
    $num_page = floor($id_like / 3);
    $num_page--;
    $sql = mysqli_query($db, 'UPDATE posts SET likes = likes + 1 WHERE id = '.$id_like.'');
    header('Location: /index.php?page='.urlencode($num_page));
    exit();
}
```
Запись нового комментарии в БД:
```php
<?php
if(isset($_GET['comment'])){
    if(isset($_GET['com'])){
        $post_id = $_GET['com'];
        $comment = $_GET['comment'];
        $len = strlen($comment);
        if ($len < 3 or $len > 120){
            $err_message = urldecode('Текст должен содержать не менее 4 и не более 120 символов');
            header('Location: index.php?err_message='.$err_message);
            exit();
        }
        $time = date("d-m-y H:i");
        $id = 0;
        $sql = mysqli_query($db, "INSERT INTO comments (id, post_id, text, time) VALUES ('$id', '$post_id', '$comment', '$time')");
    }
}
header('Location: /index.php');
exit();
?>
```
