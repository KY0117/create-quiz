<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>log in</title>
</head>
<body>
    <?php
    session_start();
    if(isset($_POST['logout'])){
        unset($_SESSION['loginusername']);
        unset($_SESSION['loginpassword']);
        echo "ログアウトしました。<br><br>";
    }

    $dsn = '********';
    $user = '********';
    $password = '********';
    $_SESSION['dsn']=$dsn;
    $_SESSION['user']=$user;
    $_SESSION['password']=$password;

    echo "ユーザーネームとパスワードを入力してください。<br>";
    echo'<form action="project.php" method="post">';
    echo'<input type="text" name="loginusername" placeholder="user name">';
    echo'<input type="text" name="loginpassword" placeholder="password">';
    echo "<button onclick='location.href='project.php''>Log in</button>";
    echo'</form>';

    echo "新しくアカウントを作る方はこちら(パスワードは8文字以上)<br>";
    echo'<form action="project.php" method="post">';
    echo'<input type="text" name="loginnewusername" placeholder="New user name">';
    echo'<input type="text" name="loginnewpassword" placeholder="password">';
    echo "<button onclick='location.href='project.php''>Log in</button>";
    echo'</form>';

    ?>
 
</body>
</html>