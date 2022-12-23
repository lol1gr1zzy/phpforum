<?php
    $db = mysqli_connect ("phpforum","root","","forum");
?>
<!DOCTYPE html>
<script>
	function show_comments(id){
		//alert(id);
		let c = document.getElementById("c"+id);
		c.removeAttribute("hidden");

		let b = document.getElementById("btn"+id);
		b.textContent = "Скрыть комментарии";

		b.setAttribute("onClick", "hide_comments('"+id+"')");
	}

	function hide_comments(id) {
		let c = document.getElementById("c"+id);
		c.setAttribute("hidden", true);

		let b = document.getElementById("btn"+id);
		b.textContent = "Показать комментарии";

		b.setAttribute("onClick", "show_comments('"+id+"')");
	}
</script>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Добро пожаловать на форум</h1>
    <form action="public.php" method="POST" enctype="multipart/form-data">
        <textarea name="note" cols="30" rows="10" placeholder="Ваш текст"></textarea>
        <br><input name="post" type="submit"></br>
        <br><input type="file" name="image"></br>
        <strong><?php
            if(isset($_GET['msg'])){
            echo $_GET['msg'];
            }
            if (isset($_GET['err_message'])){
                echo $_GET['err_message'];
            }
            ?>
        </strong>
    </form>
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
mysqli_close($db);

?>
<?php for ($p = 0; $p < $page_count; $p++) :?>
<a href="?page=<?php echo $p; ?>"><button><?php echo $p + 1; ?></button></a>
<?php endfor; ?>
</body>
</html>
