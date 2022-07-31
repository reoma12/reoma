<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>

<body>

<?php
    //変数の定義//
        $name = filter_input(INPUT_POST, 'name');
        if(empty($name)){
            $name = "名無し";
        }
        $comment = filter_input(INPUT_POST, 'str');
        $date = date("Y/m/d H:m:s");
        $pass = filter_input(INPUT_POST, 'pass');
        if(empty($pass)){
            $pass = 12144121;
        }
        $delete = filter_input(INPUT_POST, 'delete');
        $edit_post = filter_input(INPUT_POST, 'edit_post');
        $arrange =filter_input(INPUT_POST, 'arrange');
        $A_pass = filter_input(INPUT_POST, 'A_pass');
        $D_pass = filter_input(INPUT_POST, 'D_pass');
        $name_input="";
        $text_input="";
        $pass_input="";
    
    
    //データベースへの接続
        $dsn = 'mysql:dbname=ユーザー名db;host=localhost';
        $user = 'ユーザー名';
        $password = 'パスワード';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //データを登録するためのテーブルを作成
        $sql ="CREATE TABLE IF NOT EXISTS mission5"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date TEXT,"
        . "pass TEXT"
        .");";
        $stmt = $pdo->query($sql);
    
    //新規投稿
        if(!empty($name) && !empty($comment) && empty($edit_post)){
        //テーブル内にデータ（レコード）の登録
        $sql = $pdo -> prepare("INSERT INTO mission5 (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
        $sql -> execute();
        echo "新規投稿を受け付けました。"."<hr>";
        }
        
    //削除フォーム
        //パスワードなし
        if(!empty($delete) && empty($D_pass)){
            echo "パスワードを入力してください。"."<hr>";
        }
        //パスワードあり
        if(!empty($delete) && !empty($D_pass)){
        //データレコードの削除
        $id = $delete ;
        $pass = $D_pass ;
        $stmt = $pdo->prepare("SELECT * FROM mission5 WHERE id = :id");
        $stmt->bindParam( ':id', $id, PDO::PARAM_INT);
        $res = $stmt->execute();
        $data = $stmt->fetch();
        if($data[4] == $pass){
        $sql = 'delete  FROM mission5 where :id=id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        echo "投稿番号"."$delete"."の削除が完了しました。"."<hr>";
        }else{
            echo "パスワードが間違っています。"."<hr>";
        }
        }
        
        
    //編集フォーム
        //パスワードなし
        if(!empty($arrange) && empty($A_pass)){
            echo "パスワードを入力してください。"."<hr>";
        }
        if(!empty($arrange) && !empty($A_pass)){
        $id = $arrange ;
        $pass =$A_pass ;
        $stmt = $pdo->prepare("SELECT * FROM mission5 WHERE id = :id");
        $stmt->bindParam( ':id', $id, PDO::PARAM_INT);
        $res = $stmt->execute();
        $data = $stmt->fetch();
        if($data[4] == $pass){
        $name_input = $data["name"];
        $text_input = $data["comment"];
        $pass_input = $data["pass"];
        }else{
            echo "パスワードが間違っています。"."<hr>";
            $arrange ="";
        }
        }
        //新規投稿との区別する
        if(!empty($edit_post) && !empty($comment)){
            //入力されているデータレコードの更新
            $id = $edit_post; //変更する投稿番号
            $sql = 'UPDATE mission5 SET name=:name,comment=:comment,date=:date,pass =:pass WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
            $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
            $stmt -> bindParam(':pass', $pass, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            echo "投稿番号"."$id"."の編集が完了しました。". "<hr>";
        }
        
        //画面上に出力
        $sql = 'SELECT * FROM mission5';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id'].',';
            echo $row['name'].',';
            echo $row['comment'].',';
            echo $row['date'];
            echo $row['pass'];
            echo "<hr>";
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
    
</body>