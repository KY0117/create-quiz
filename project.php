<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>my projects</title>
</head>
<body>
    <?php
    session_start();
    if(!empty($_POST['loginusername']) && !empty($_POST['loginpassword'])){
        $q=1;
        $k=0;
        if(file_exists("users.text")){
            $lines=file("users.text");
            foreach($lines as $line){
                $eachdata=explode("<>[}]",$line);
                if($eachdata[0]==$_POST['loginusername'] && $eachdata[1]==$_POST['loginpassword']){
                    $_SESSION['loginusername']=$_POST['loginusername'];
                    $_SESSION['loginpassword']=$_POST['loginpassword'];
                    $k=1;
                }
            }
        }
        if($k==0){
            echo "ユーザーネームまたはパスワードが間違っています。";
        }
    }elseif(!empty($_POST['loginusername'])){
        $q=1;
        echo "パスワードを入力してください。";
    }elseif(!empty($_POST['loginpassword'])){
        $q=1;
        echo "ユーザーネームを入力してください。";
    }
    if(!empty($_POST['loginnewusername']) && !empty($_POST['loginnewpassword'])){
        $q=1;
        $k=0;
        if(file_exists("users.text")){
            $lines=file("users.text");
            foreach($lines as $line){
                $eachdata=explode("<>[}]",$line);
                if($eachdata[0]==$_POST['loginnewusername']){
                    $k=1;
                    echo "このユーザーネームはすでに使われています。<br>";
                }
            }
        }
        if(mb_strlen($_POST['loginnewpassword'])>=8){
            if($k==0){
                $fp=fopen("users.text","a");
                fwrite($fp,$_POST['loginnewusername']."<>[}]".$_POST['loginnewpassword']."<>[}]".PHP_EOL);
                fclose($fp);
                $_SESSION['loginusername']=$_POST['loginnewusername'];
                $_SESSION['loginpassword']=$_POST['loginnewpassword'];
            }
        }else{
            echo "パスワードが短すぎます。";
        }
    }elseif(!empty($_POST['loginnewusername'])){
        $q=1;
        echo "パスワードを入力してください。";
    }elseif(!empty($_POST['loginnewpassword'])){
        $q=1;
        echo "ユーザーネームを入力してください。";
    }
    if(isset($_SESSION['loginusername'])){
        $dsn = $_SESSION['dsn'];
        $user = $_SESSION['user'];
        $password = $_SESSION['password'];
        $_SESSION['dsn']=$dsn;
        $_SESSION['user']=$user;
        $_SESSION['password']=$password;

        echo "<form action='login.php' method='POST'>";
        echo "<input type='hidden' name='logout' value='logout'>";
        echo "<button onclick='location.href='login.php''>Log out</button>";
        echo "</form>";

        echo'<form action="" method="post">';
        echo'<input type="text" name="tablename" placeholder="New project name">';
        echo'<input type="submit">';
        echo'</form>';

        function create_table($dsn, $user, $password){
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $name="techbaseyaguchirやbmlw美ぇ".$_POST["tablename"]."rやbmlw美ぇ".$_SESSION['loginusername'];
            $sql = "CREATE TABLE IF NOT EXISTS $name"
            ." ("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"
            . "question char(255),"
            . "choice char(255),"
            . "explanation char(255),"
            . "image char(255)"
            .");";
            $stmt = $pdo->query($sql);
        }

        function delete_table($dsn, $user, $password){
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $sql ='SHOW TABLES';
            $result = $pdo -> query($sql);
            foreach ($result as $row){
                if(isset($_POST["$row[0]delete"])){
                    $sql = "DROP TABLE $row[0]";
                    $stmt = $pdo->query($sql);
                }
            }    
        }

        function show_table($dsn, $user, $password, $u){
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $sql ='SHOW TABLES';
            $result = $pdo -> query($sql);
            echo "<hr>";
            foreach ($result as $row){
                $project=explode("rやbmlw美ぇ",$row[0]);
                if($project[0]=="techbaseyaguchi"){
                    if($project[2]==$u){
                        if(isset($_POST["$row[0]rename"])){
                            echo "<form action='' method='post'>";
                            echo "<input type='text' name='$row[0]newname' placeholder='New project name'>";
                            echo "<input type='submit'>";
                            echo "</form>";
                            echo "<div style='background-color: lightblue' >";
                            echo "<div style='display:inline-flex'>";
                        }else{
                            echo $project[1];
                            echo "<div style='background-color: lightblue' >";
                            echo "<div style='display:inline-flex'>";
                            echo "<form action='' method='post'>";
                            echo "<input type='hidden' name='$row[0]newname'>";
                            echo "</form>";
                        }
                        echo "<form action='' method='post'>";
                        echo "<input type='submit' name='$row[0]rename' value='rename'>";
                        echo "<input type='submit' name='$row[0]delete' value='delete'>";
                        echo "</form>";
                        echo "<form action='eachproject.php' method='POST' target='_blank'>";
                        echo "<input type='hidden' name='useprojectname' value='$row[0]'>";
                        echo "<button onclick='location.href='eachproject.php''>open</button>";
                        echo "</form>";
                        echo "</div></div>";
                        echo '<hr>';
                    }
                }
            }  
        }

        function edit_table($dsn, $user, $password ,$n){
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $sql ='SHOW TABLES';
            $result = $pdo -> query($sql);
            foreach ($result as $row){
                if(isset($_POST["$row[0]newname"])){
                    $newname="techbaseyaguchirやbmlw美ぇ".$_POST["$row[0]newname"]."rやbmlw美ぇ".$n;
                    $sql ="ALTER TABLE $row[0] RENAME TO $newname";
                    $result = $pdo -> query($sql);
                }
            }
        }

        if(isset($_POST["tablename"])){
            create_table($dsn, $user, $password);
        }else{
            delete_table($dsn, $user, $password,$_SESSION['loginusername']);
            edit_table($dsn, $user, $password, $_SESSION['loginusername']);
        }
        show_table($dsn, $user, $password,$_SESSION['loginusername']);
    }elseif(empty($q)){
        echo "ユーザーネームとパスワードを入力してログインしてください。";
    }
    ?>

</body>
</html>