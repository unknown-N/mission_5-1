<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>mission_5-1</title>
</head>
<body>
	<?php 
	//データベース接続
   	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //テーブル作成
	$sql = "CREATE TABLE IF NOT EXISTS mission5"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "date char(32),"
    . "password char(100),"
	. "comment TEXT "
	.");";
	$stmt = $pdo->query($sql);
	//新規投稿
    if(isset($_POST["submit"]) && empty($_POST["hiddeneditnum"]) &&
    !empty($_POST["name"] && $_POST["comment"] && $_POST["password"])){
		$sql = $pdo -> prepare("INSERT INTO mission5 (name, comment ,date ,password) 
        VALUES (:name, :comment, :date, :password)");
		$sql -> bindParam(':name', $name, PDO::PARAM_STR);
		$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
		$sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':password', $password, PDO::PARAM_STR);
		$name     = $_POST["name"];
		$comment  = $_POST["comment"];
        $password = $_POST["password"];
        $date     = date("Y/m/d h:i:s");
		$sql -> execute();
	}else{
        ;
	}
    //編集(のために、文字列をフォームへ)
    $editname      = "";
	$editcomment   = "";
    $editpassword  = "";
    $hiddeneditnum = "";
    if( isset($_POST["editsubmit"]) && !empty($_POST["editnum"] && $_POST["editpass"])){
        $sql = 'SELECT * FROM mission5';
	    $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($_POST["editnum"]==$row['id'] && $_POST["editpass"]==$row['password']){
		        $sql = $pdo -> prepare("INSERT INTO mission5 (id,name, comment ,password) 
                VALUES (:id, :name, :comment, :password)");  
                $sql -> bindParam(':id', $hiddeneditnum, PDO::PARAM_STR);
                $sql -> bindParam(':name', $editname, PDO::PARAM_STR);
		        $sql -> bindParam(':comment', $editcomment, PDO::PARAM_STR);
		        $sql -> bindParam(':password', $editpassword, PDO::PARAM_STR);
                $hiddeneditnum = $row['id'];
                $editname      = $row['name'];
                $editcomment   = $row['comment'];
                $editpassword  = $row['password'];
		        $sql -> execute();
            } 
        }  
    }else{
        ;
    }
    //編集
    if(isset($_POST["submit"]) && 
    !empty($_POST["hiddeneditnum"] && $_POST["name"] && $_POST["comment"] && $_POST["password"])){
        $sql = 'SELECT * FROM mission5';
	    $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($_POST["hiddeneditnum"]==$row['id'] ){ 
                $id = $_POST["hiddeneditnum"];
	            $name     = $_POST["name"];
	            $comment  = $_POST["comment"];
                $date     = date("Y/m/d h:i:s");
                $password = $_POST["password"];
	            $sql = 'UPDATE mission5 SET name=:name,comment=:comment,date=:date,password=:password WHERE id=:id';
	            $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_STR);
	            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
	            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
	            $stmt->execute();
                //hiddenフォームに入った番号が消えなかったのでNULLを書き込む
                $sql = $pdo -> prepare("INSERT INTO mission5 (id) VALUES (:id)");
		        $sql -> bindParam(':id', $hiddeneditnum, PDO::PARAM_STR);
		        $hiddeneditnum    = NULL;
                $stmt->execute();
            }
        }
    }
    else{
        ;
    }
   
	//削除
    if(isset($_POST["deletesubmit"]) && !empty($_POST["deletenum"] && $_POST["deletepass"])){
        $sql = 'SELECT * FROM mission5';
	    $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
	        if($_POST["deletenum"]==$row['id'] && $_POST["deletepass"]==$row['password']){
                $hiddeneditnum = $row['id'];
	            $sql = 'delete from mission5 where id=:id';
            	$stmt = $pdo->prepare($sql);
            	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	            $stmt->execute();
	        }   
        }   
    }else{
        ;
    }
	?>
    <form action="" method="POST" >
    【書き込み方法】<br>
    名前、コメント、パスワードを入力→送信ボタンをクリック<br>
    ※全項目入力してください<br>
    ※パスワード設定により、編集と削除が可能になります。<br>
    <input type="text" name="name" placeholder="名前" value="<?php echo $editname; ?>">
    <input type="text" name="comment" placeholder= "コメント" value="<?php echo $editcomment; ?>">
    <input type="text" name="password" placeholder="パスワード" value="<?php echo $editpassword; ?>">
    <input type="submit" name="submit" value="送信"><br>
    【編集方法】<br>
    編集したい番号、設定したパスワードを入力→編集ボタンをクリック→
    内容を編集(書き込みフォームの項目全て入力必須)→送信ボタンをクリック<br>
    <input type="text" name="editnum" placeholder="編集番号">
    <input type="text" name="editpass" placeholder="パスワード">
    <input type="submit" name="editsubmit" value="編集"><br>
    <input type="hidden" name="hiddeneditnum" value="<?php echo $hiddeneditnum; ?>">
    【削除方法】<br>
    削除したい番号、設定したパスワードを入力→削除ボタンをクリック<br>
    <input type="text" name="deletenum" placeholder="削除番号" >
    <input type="text" name="deletepass" placeholder="パスワード">
    <input type="submit" name="deletesubmit" value="削除">
    </form>
    <?php
    //データ表示
    $sql = 'SELECT * FROM mission5';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'].'<br>';
	}
    ?>
</body>
</html>