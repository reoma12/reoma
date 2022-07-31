<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_3-5</title>
</head>

<body>
    

     <?php 
        //変数の定義//
        $name = filter_input(INPUT_POST, 'name');
        $comment = filter_input(INPUT_POST, 'str');
        $password = filter_input(INPUT_POST, 'pass');
        if(empty($password)){
            $password = 12144121;
        }
        $delete = filter_input(INPUT_POST, 'delete');
        $edit_post = filter_input(INPUT_POST, 'edit_post');
        $arrange =filter_input(INPUT_POST, 'arrange');
        $A_pass = filter_input(INPUT_POST, 'A_pass');
        $D_pass = filter_input(INPUT_POST, 'D_pass');
        $date = date("Y/m/d H:m:s");
        $filename = "mission3-5.txt";
        $num = 1;
        if (file_exists($filename)) {
                $num = count(file($filename))+1;
            }
        $output = "$num"."<>"."$name"."<>"."$comment"."<>"."$date"."<>"."$password"."<>";
        $name_input="";
        $text_input="";
        $pass_input="";
       
        //新規投稿
        if(!empty($name) && !empty($comment) && empty($edit_post)){
            $fp = fopen($filename, "a");
            flock($fp, LOCK_EX);
            fwrite($fp, $output.PHP_EOL);
            fclose($fp);
            echo "新規投稿を受付けました。";
            
        //新規投稿で名前がない時、名無しとする
            }elseif(!empty($comment) && empty($name) && empty($edit_post)){
            $name = "名無し";
            $output = "$num"."<>"."$name"."<>".$comment."<>"."$date"."<>"."$password"."<>";
            $fp = fopen($filename, "a");
            flock($fp, LOCK_EX);
            fwrite($fp, $output.PHP_EOL);
            fclose($fp);
            echo "新規投稿を受付けました。";
            }
        
        //削除フォーム
        //パスワードなし
        if(!empty($delete) && empty($D_pass)){
            echo "パスワードを入力してください。";
        }
        //パスワードあり
        if(!empty($delete) && !empty($D_pass)){
            $lines = file($filename,FILE_IGNORE_NEW_LINES);
            $fp = fopen($filename, "w+");
            $D_pass = filter_input(INPUT_POST, 'D_pass');
            for($i = 0; $i <count($lines); $i++){
                $line = explode("<>",$lines[$i]);
                $postnum = $line[0];
                $postpass =$line[4];
                if($postnum != $delete){
                    fwrite($fp, $lines[$i].PHP_EOL);
                }elseif($postnum == $delete && $postpass != $D_pass){
                    fwrite($fp, $lines[$i].PHP_EOL);
                    echo "パスワードが間違っています。";
                }elseif($postnum == $delete && $postpass == $D_pass){
                    echo "投稿番号"."$delete"."の削除が完了しました。";
                    continue;
                }
            }
            //投稿番号を書き換える
            $newlines = file($filename,FILE_IGNORE_NEW_LINES);
            $fp =fopen($filename, "w+");
             for($i = 0; $i<count($newlines); $i++){
                $newline = explode("<>", $newlines[$i]);
                 if($newline[0] != $i+1){
                    $newline[0] = $i+1;
                }
                $new =join("<>", $newline);
                fwrite($fp, $new.PHP_EOL);
            }
            fclose($fp);
        }
        
        //編集フォーム
        //パスワードないとき
        if(!empty($arrange) && empty($A_pass)){
            echo "パスワードを入力してください。";
        }
        //パスワードあるとき
        if(!empty($arrange) && !empty($A_pass)){
            $lines = file($filename,FILE_IGNORE_NEW_LINES);
            for($i = 0; $i <count($lines); $i++){
                $line = explode("<>",$lines[$i]);
                $postnum = $line[0];
                $postname = $line[1];
                $postcomment = $line[2];
                $postpass = $line[4];
                if($postnum == $arrange && $postpass == $A_pass){
                  $name_input = $postname;
                  $text_input = $postcomment;
                  $pass_input = $postpass;
                }elseif($postnum == $arrange && $postpass != $A_pass){
                    echo "パスワードが間違っています。";
                    continue;
                }
            }
        }
        
        //新規投稿との区別する
            if(!empty($edit_post) && !empty($comment)){
            $name = filter_input(INPUT_POST, 'name');
            $comment = filter_input(INPUT_POST, 'str');
            $password = filter_input(INPUT_POST, 'pass');
            $lines = file($filename,FILE_IGNORE_NEW_LINES);
            $fp =fopen($filename, "w+");
            for($i = 0; $i <count($lines); $i++){
                $line = explode("<>",$lines[$i]);
                $postnum = $line[0];
                if($postnum != $edit_post){
                    fwrite($fp, $lines[$i].PHP_EOL);
                }elseif($postnum == $edit_post){
                    $output = "$edit_post"."<>"."$name"."<>"."$comment"."<>"."$date"."<>"."$password"."<>";
                    fwrite($fp,$output.PHP_EOL);
                    echo "投稿番号"."$edit_post"."の編集が成功しました。";
                }
            }
            }
        ?>

    <!-- フォーム -->
    <form action="" method="post">
        投稿用:
        <p><input type="text" name="name" size = "10" value="<?php echo$name_input;?>" placeholder="名前"></p>
        <p><input type ="text" name ="str" size ="20" value="<?php echo$text_input;?>" placeholder="コメント"></p>
        <input type ="password" name ="pass" size "20" value="<?php echo$pass_input;?>" placeholder="パスワード">
        <input type ="submit" name ="submit" value ="投稿">
        <br>
        <!-- 編集用隠しフォーム　-->
        <input type ="hidden" name ="edit_post" value ="<?php echo$arrange;?>">
        
        <!-- 削除用フォーム -->
        <br>
        削除用:
        <p><input type ="number" name ="delete" placeholder="投稿番号"></p>
        <input type ="password" name ="D_pass" size "20" placeholder="パスワード">
        <input type ="submit" name ="submit" value ="削除">
        <br>
        <!--編集用フォーム -->
        <br>
        編集用:
        <p><input type ="number" name ="arrange" placeholder="投稿番号"></p>
        <input type ="password" name ="A_pass" size "20" placeholder="パスワード">
        <input type ="submit" name ="submit" value ="編集">
    </form>
    
    <!--　出力 -->
<?php 
if(!empty($name) ||  !empty($comment) || !empty($edit_post) || !empty($delete)){
    if($comment == "リセット"){
                fopen($filename, "w+");
    }
}


$lines =file($filename,FILE_IGNORE_NEW_LINES);
            for($i =0; $i < count($lines); $i++){
                $line = explode("<>",$lines[$i]);
                for($n = 0; $n <4; $n++){
                    echo $line[$n]."&nbsp";
                }
                    echo "<br>";
                } 

?>

</body>
    
    
    
