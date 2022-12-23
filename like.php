<?php
    $db = mysqli_connect ("phpforum","root","","forum");
?>
<?php
if(isset($_POST['like'])){
    $id_like = $_POST['like'];
    $num_page = floor($id_like / 3);
    $num_page--;
    $sql = mysqli_query($db, 'UPDATE posts SET likes = likes + 1 WHERE id = '.$id_like.'');
    header('Location: /index.php?page='.urlencode($num_page));
    exit();
}
if(isset($_POST['dislike'])){
    $id_dislike = $_POST['dislike'];
    $num_page = floor($id_dislike / 3);
    $num_page--;
    //echo $_POST['dislike'];
    $sql = mysqli_query($db, 'UPDATE posts SET dislikes = dislikes + 1 WHERE id = '.$id_dislike.'');
    //echo $id_dislike;
}
header('Location: /index.php?page='.urlencode($num_page));
exit();
?>