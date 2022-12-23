<?php
    $db = mysqli_connect ("phpforum","root","","forum");
?>

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