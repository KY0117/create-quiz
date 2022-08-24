<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>生成</title>
</head>
<body>
    <?php
    session_start();
    if(isset($_POST['useprojectname'])){
        $projectname = $_POST['useprojectname'];
        $_SESSION['projectname'] = $projectname;
    }elseif(isset($_SESSION['projectname'])){
        $projectname = $_SESSION['projectname'];
    }
    $projectname2=explode("rやbmlw美ぇ",$projectname);
    $name=$projectname2[1];

    $dsn = $_SESSION['dsn'];
    $user = $_SESSION['user'];
    $password = $_SESSION['password'];
    $_SESSION['dsn']=$dsn;
    $_SESSION['user']=$user;
    $_SESSION['password']=$password;
    $filename=$projectname2[0]."001100".$projectname2[1].".php";
    $fp=fopen($filename,"w");
    $text=
"<html lang='ja'>
<head>
    <meta charset='UTF-8'>
    <title>$name</title>
</head>
<body>
    <?php";
    fwrite($fp,$text.PHP_EOL);
    $pdo= new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    $sql= "SELECT * FROM $projectname";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    fwrite($fp,'if(empty($_POST["submit"])){'.PHP_EOL);
    fwrite($fp,"echo \"<form action='' method='post'>\";".PHP_EOL);
    $q=0;
    foreach ($results as $row){
        $q=$q+1;
        $rowidanswer=$row['id']."answer";
        $question=$row['question'];
        fwrite($fp,"echo \"問題$q: \";".PHP_EOL);
        if(!empty($question)){
            fwrite($fp,"echo '$question<br>';".PHP_EOL);
        }else{
            fwrite($fp,"echo '<br>';".PHP_EOL);
        }
        $image=$row['image'];
        if(!empty($image)){
            fwrite($fp,"echo \"<img src=$image alt='' height='200'><br>\";".PHP_EOL);
        }
        $choices=explode("<<>>",$row['choice']);
        foreach($choices as $choice){
            $components=explode("[[]]",$choice);
            if(!empty($components[1])){
                fwrite($fp,"echo '$components[0]'.' '.'$components[1]'.'<br>';".PHP_EOL);
            }else{
                fwrite($fp,"echo '$components[0]'.'<br>';".PHP_EOL);
            }
        }
        fwrite($fp,"echo \"<select name='$rowidanswer'>\";".PHP_EOL);
        foreach($choices as $choice){
            $components=explode("[[]]",$choice);
            fwrite($fp,"echo \"<option value=$components[0]>$components[0]</option>\";".PHP_EOL);
        }
        fwrite($fp,"echo \"</select><br><br>\";".PHP_EOL);
    }
    fwrite($fp,"echo \"<input  name='submit' type='submit'>\";".PHP_EOL);
    fwrite($fp,"echo '</form>';".PHP_EOL);
    fwrite($fp,"}".PHP_EOL);
    fwrite($fp,'if(!empty($_POST["submit"])){'.PHP_EOL);
    fwrite($fp,'$point=0;'.PHP_EOL);
    foreach($results as $row){
        $rowidanswer='\''.$row['id'].'answer'.'\'';
        $choices=explode("<<>>",$row['choice']);
        fwrite($fp,'if(!empty($_POST['.$rowidanswer.'])){'.PHP_EOL);
        if(!empty($row['choice'])){
            foreach($choices as $choice){
                $components=explode("[[]]",$choice);
                fwrite($fp,'if($_POST['.$rowidanswer.']=='.$components[0].'){'.PHP_EOL);
                fwrite($fp,'$point=$point+'."$components[2];".PHP_EOL);
                fwrite($fp,"}".PHP_EOL);
            }
        }
        fwrite($fp,"}".PHP_EOL);
    }
    fwrite($fp,'echo "得点:"."<strong><strong><font size=\'10\'>".$point."</font></strong></strong>"."<br>";'.PHP_EOL);
    fwrite($fp,"echo \"<hr>\";");
    $q=0;
    foreach($results as $row){
        $q=$q+1;
        $rowidanswer=$row['id']."answer";
        $question=$row['question'];
        fwrite($fp,"echo \"問題$q: \";".PHP_EOL);
        if(!empty($question)){
            fwrite($fp,"echo '$question<br>';".PHP_EOL);
        }else{
            fwrite($fp,"echo '<br>';".PHP_EOL);
        }
        $image=$row['image'];
        if(!empty($image)){
            fwrite($fp,"echo \"<img src=$image alt='' height='200'><br>\";".PHP_EOL);
        }
        if(!empty($row['choice'])){
            $choices=explode("<<>>",$row['choice']);
            foreach($choices as $choice){
                $components=explode("[[]]",$choice);
                fwrite($fp,"if($components[0]==".'$_POST["'.$rowidanswer."\"]){".PHP_EOL);
                fwrite($fp,'$point='.$components[2].";".PHP_EOL);
                fwrite($fp,'$sign='."\"$components[3]\"".";".PHP_EOL);
                fwrite($fp,"}".PHP_EOL);
            }
            fwrite($fp,'echo $sign.\' \'.$point.\'点<br>\';'.PHP_EOL);
        }
        fwrite($fp,'$p=$_POST[\''.$rowidanswer.'\'];'.PHP_EOL);
        fwrite($fp,'echo "あなたの答え:$p"."<br>";'.PHP_EOL);
        if(!empty($row['explanation'])){
            fwrite($fp,'echo "解説:'.$row['explanation']."\".'<br>';".PHP_EOL);
        }
        if(!empty($row['choice'])){
        $choices=explode("<<>>",$row['choice']);
            foreach($choices as $choice){
                $components=explode("[[]]",$choice);
                if(!empty($components[1])){
                    fwrite($fp,"echo '$components[0]'.' '.'$components[1]'.'$components[3]'.' '.'$components[2]'.'点<br>';".PHP_EOL);
                }else{
                    fwrite($fp,"echo '$components[0]'.'  '.'$components[3]'.' '.'$components[2]'.'点<br>';".PHP_EOL);
                }
            }
        }
        fwrite($fp,"echo \"<hr>\";");
    }
    fwrite($fp,"}".PHP_EOL);
    $text=
    "?>
</body>
</html>";
    fwrite($fp,$text);
    fclose($fp);
    echo "<form action='$filename' method='POST'>";
    echo "<button onclick='location.href=\"$filename\"'>移動</button>";
    echo "</form>";
    
    ?>
 
</body>
</html>