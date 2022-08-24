<?php 
    session_start();
    if(isset($_POST['useprojectname'])){
        $projectname = $_POST['useprojectname'];
        $_SESSION['projectname'] = $projectname;
    }elseif(isset($_SESSION['projectname'])){
        $projectname = $_SESSION['projectname'];
    }
    $projectname2=explode("rやbmlw美ぇ",$projectname);
    

?>

<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo $projectname2[1];?></title>
</head>
<body >
    <?php

    echo "<strong>$projectname2[1]</strong>";

    $dsn = $_SESSION['dsn'];
    $user = $_SESSION['user'];
    $password = $_SESSION['password'];
    $_SESSION['dsn']=$dsn;
    $_SESSION['user']=$user;
    $_SESSION['password']=$password;

    function show_questions($dsn, $user, $password,$projectname){
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        $sql = "SELECT * FROM $projectname";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        echo "<hr>";
        foreach ($results as $row){
            $rowid=$row['id'];
            $rowidopen=$row['id']."open";
            $rowidclose=$row['id']."close";
            $rowiddelete=$row['id']."delete";
            echo "<div style='display:inline-flex'>";
            echo $rowid.'<br>';
            echo "<form action='' method='POST'>";
            echo "<input type='submit' name='$rowidopen' value='open'>";
            echo "<input type='submit' name='$rowidclose' value='close'>";
            echo "<input type='submit' name='$rowiddelete' value='delete'>";
            echo "</form>";
            echo "</div><br>";
            $keep=$projectname.$rowid;
            if(isset($_POST[$rowidclose])||empty($_SESSION[$keep])){
                $_SESSION[$keep]=0;
            }
            if(isset($_POST[$rowidopen]) || $_SESSION[$keep]==1){
                show_question($dsn, $user, $password,$projectname,$rowid);
                $_SESSION[$keep]=1;
            }else{
                echo "Q: ".mb_substr($row['question'],0,20).'...';
            }
            echo '<hr>';
        }
    }

    function show_question($dsn, $user, $password,$projectname,$rowid){
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        $sql = "SELECT * FROM $projectname";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            $rowid2 = $row['id'];
            if($rowid2==$rowid){
                show_parts($row,$rowid,"question");
                show_choice($row,$rowid,"choice");
                show_parts($row,$rowid,"explanation");
                show_image($row,$rowid,"image");
            }
        }
    }

    function show_parts($row,$rowid,$word){
        $rowedit=$rowid."edit".$word;
        echo "<div style='display:inline-flex'>";
        echo "<form action='' method='post'>";
        echo "<input type='submit' name='$rowedit' value='編集'>";
        echo "</form>";
        echo "$word: ";
        $now=$row[$word];
        $rownew=$rowid.$word;
        if(isset($_POST[$rowedit])){
            echo "<form action='' method='post'>";
            echo "<input type='text' name='$rownew' value='$now'>";
            echo "<input type='submit'>";
            echo "</form>";
        }else{
            echo $now.'<br>';
            echo "<form action='' method='post'>";
            echo "<input type='hidden' name='$rownew'>";
            echo "</form>";
        }
        echo "</div><br>";
    }

    function show_choice($row,$rowid,$word){
            $choices=explode("<<>>",$row[$word]);
            $rowidadd=$rowid."add".$word;
            echo "<form action='' method='post'>";
            echo $word;
            echo "<input type='submit' name='$rowidadd' value='選択肢追加'>";
            echo "</form>";
            if(isset($row[$word])){
                $rownewcon=$rowid.$word."context";
                $rowdeleteall=$rowid."delete".$word;
                $rowpointall=$rowid."point".$word;
                $rowsignall=$rowid."sign".$word;
                foreach($choices as $choice){
                    if(isset($choice)){
                        $compounds=explode("[[]]",$choice);
                        $rowedit=$rowid."edit".$word.$compounds[0];
                        $rownew=$rowid.$word.$compounds[0];
                        $rowidedit=$rowid."edit".$word.$compounds[0];
                        $rowdelete=$rowid."delete".$word.$compounds[0];
                        $rowpoint=$rowid."point".$word.$compounds[0];
                        $rownewpoint=$rowid."newpoint".$word.$compounds[0];
                        $rowsign=$rowid."sign".$word.$compounds[0];
                        $rownewsign=$rowid."newsign".$word.$compounds[0];

                        echo "<div style='display:inline-flex'>";
                        echo "<form action='' method='POST'>";
                        echo "<input type='submit' name='$rowdelete' value='削除'>";
                        echo "<input type='hidden' name='$rowdeleteall'>";
                        echo "</form>";

                        echo "<form action='' method='POST'>";
                        echo "<input type='submit' name='$rowsign' value='評価変更'>";
                        echo "</form>";
                        echo "<form action='' method='POST'>";
                        if(isset($_POST[$rowsign])){
                            echo "<select name='$rownewsign'>";
                            echo "<option value=✖️>✖️</option>";
                            echo "<option value=△>△</option>";
                            echo "<option value=○>○</option>";
                            echo "<option value=◎>◎</option>";
                            echo "</select>";
                            echo "<input type='submit' value='決定'>";
                            echo "<input type='hidden' name='$rowsignall'>";
                        }else{
                            if(isset($compounds[3])){
                                echo $compounds[3];
                            }
                            echo "<input type='hidden' name='$rownewsign'>";
                        }
                        echo "</form>";

                        echo "<form action='' method='POST'>";
                        echo "<input type='submit' name='$rowpoint' value='点数変更'>";
                        echo "</form>";
                        echo "<form action='' method='POST'>";
                        if(isset($_POST[$rowpoint])){
                            echo "<input type='text' name='$rownewpoint' value=";
                            if(isset($compounds[2])){
                                echo "$compounds[2]";
                            }
                            echo ">";
                            echo "<input type='hidden' name='$rowpointall'>";
                            echo "<input type='submit'>";
                        }else{
                            if(isset($compounds[2])){
                                echo "$compounds[2]";
                            }
                            echo "<input type='hidden' name='$rownewpoint'>";
                        }
                        echo "</form>";

                        echo "<form action='' method='POST'>";
                        echo "<input type='submit' name='$rowidedit' value='編集'>";
                        echo "</form>";
                        echo "<form action='' method='POST'>";
                        echo "$word($compounds[0]): ";
                        if(isset($_POST[$rowidedit])){
                            echo "<input type='text' name='$rownew' value=";
                            if(isset($compounds[1])){
                                echo "$compounds[1]";
                            }
                            echo ">";
                            echo "<input type='hidden' name='$rownewcon'>";
                            echo "<input type='submit'>";
                        }else{
                            if(isset($compounds[1])){
                                echo "$compounds[1]";
                            }
                            echo "<input type='hidden' name='$rownew'>";
                        }
                        echo "</form></div><br>";
                    }
                }
            }
    }

    function show_image($row,$rowid,$word){
        $now=$row[$word];
        $rowdelete=$rowid."delete".$word;
        $rowdeleteall="delete".$word;
        $rowidimagename=$rowid.$word;
        echo "<div style='display:inline-flex'>";
        echo "<form action='' method='POST'>";
        echo "<input type='submit' name='$rowdelete' value='削除'>";
        echo "<input type='hidden' name='$rowdeleteall'>";
        echo "</form>";
        echo "<section class='form-container'>";
        if(!empty($now)){
            echo "<img src=$now alt='' height='200'>";
        }
        echo '<form action="" method="post" enctype="multipart/form-data">';
        echo "<input type='file' name=$rowidimagename>";
        echo '<input type="submit" calss="btn_submit" value="送信">';
        echo '</form></section></div>';
    }

    function edit_question($dsn, $user, $password, $projectname){
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        $sql = "SELECT * FROM $projectname";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            edit_parts($dsn, $user, $password,$projectname,$row['id'],"question");
            edit_choice($dsn, $user, $password,$projectname,$row,"choice");
            edit_parts($dsn, $user, $password,$projectname,$row['id'],"explanation");
            edit_image($dsn, $user, $password,$projectname,$row['id'],"image");
        }
    }

    function edit_parts($dsn, $user, $password,$projectname,$rowid,$word){
        $rownew=$rowid.$word;
        if(isset($_POST[$rownew])){
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $sql = "UPDATE $projectname SET $word=:$word WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":$word",$_POST[$rownew], PDO::PARAM_STR);
            $stmt->bindParam(":id",$rowid, PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    function edit_image($dsn, $user, $password, $projectname,$rowid,$word){
        $rowdelete=$rowid."delete".$word;
        if(!empty($_POST[$rowdelete])){
            $null="";
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $sql = "UPDATE $projectname SET $word=:$word WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":$word",$null, PDO::PARAM_STR);
            $stmt->bindParam(":id",$rowid, PDO::PARAM_STR);
            $stmt->execute();
        }
        $rowidimagename=$rowid.$word;
        if(!empty($_FILES[$rowidimagename]['name'])){
            $imagename = $_FILES[$rowidimagename]['name'];
            $uploaded_path = 'images_after/'.$imagename;
            $result = move_uploaded_file($_FILES[$rowidimagename]['tmp_name'],$uploaded_path);
            if($result){
                $img_path=$uploaded_path;
                $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
                $sql = "UPDATE $projectname SET $word=:$word WHERE id=:id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":$word",$img_path, PDO::PARAM_STR);
                $stmt->bindParam(":id",$rowid, PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    }

    function edit_choice($dsn, $user, $password,$projectname,$row,$word){
        $rowid=$row['id'];
        $rowidadd=$rowid."add".$word;
        if(isset($_POST[$rowidadd])){
            if(isset($row[$word])){
                $choices=explode("<<>>",$row[$word]);
            }
            if(isset($choices)){
                $newnum=count($choices)+1;
                $rownewchoices=$row[$word]."<<>>".$newnum."[[]][[]]0[[]]✖️";
            }else{
                $rownewchoices="1"."[[]][[]]0[[]]✖️";
            }
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $sql = "UPDATE $projectname SET $word=:$word WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":$word",$rownewchoices, PDO::PARAM_STR);
            $stmt->bindParam(":id",$rowid, PDO::PARAM_STR);
            $stmt->execute();
        }

        $rownewcon=$rowid.$word."context";
        if(isset($_POST[$rownewcon])){
            $choices=explode("<<>>",$row[$word]);
            $n=1;
            foreach($choices as $choice){
                $compounds=explode("[[]]",$choice);
                $rownew=$rowid.$word.$compounds[0];
                if(isset($rownewchoices)){
                    if(isset($_POST[$rownew])){
                        $rownewchoices=$rownewchoices."<<>>".$n."[[]]".$_POST[$rownew]."[[]]".$compounds[2]."[[]]".$compounds[3];
                    }else{
                        $rownewchoices=$rownewchoices."<<>>".$n."[[]]".$compounds[1]."[[]]".$compounds[2]."[[]]".$compounds[3];
                    }
                }else{
                    if(isset($_POST[$rownew])){
                        $rownewchoices=$n."[[]]".$_POST[$rownew]."[[]]".$compounds[2]."[[]]".$compounds[3];
                    }else{
                        $rownewchoices=$n."[[]]".$compounds[1]."[[]]".$compounds[2]."[[]]".$compounds[3];    
                    }   
                }
                $n=$n+1;
            }
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $sql = "UPDATE $projectname SET $word=:$word WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":$word",$rownewchoices, PDO::PARAM_STR);
            $stmt->bindParam(":id",$rowid, PDO::PARAM_STR);
            $stmt->execute();
        }

        $rowsignall=$rowid."sign".$word;
        if(isset($_POST[$rowsignall])){
            $choices=explode("<<>>",$row[$word]);
            $n=1;
            foreach($choices as $choice){
                $compounds=explode("[[]]",$choice);
                $rownewsign=$rowid."newsign".$word.$compounds[0];
                if(isset($rownewchoices)){
                    if(isset($_POST[$rownewsign])){
                        $rownewchoices=$rownewchoices."<<>>".$n."[[]]".$compounds[1]."[[]]".$compounds[2]."[[]]".$_POST[$rownewsign];
                    }else{
                        $rownewchoices=$rownewchoices."<<>>".$n."[[]]".$compounds[1]."[[]]".$compounds[2]."[[]]".$compounds[3];
                    }
                }else{
                    if(isset($_POST[$rownewsign])){
                        $rownewchoices=$n."[[]]".$compounds[1]."[[]]".$compounds[2]."[[]]".$_POST[$rownewsign];
                    }else{
                        $rownewchoices=$n."[[]]".$compounds[1]."[[]]".$compounds[2]."[[]]".$compounds[3];    
                    }   
                }
                $n=$n+1;
            }
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $sql = "UPDATE $projectname SET $word=:$word WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":$word",$rownewchoices, PDO::PARAM_STR);
            $stmt->bindParam(":id",$rowid, PDO::PARAM_STR);
            $stmt->execute();
        }

        $rowpointall=$rowid."point".$word;
        if(isset($_POST[$rowpointall])){
            $choices=explode("<<>>",$row[$word]);
            $n=1;
            foreach($choices as $choice){
                $compounds=explode("[[]]",$choice);
                $rownewpoint=$rowid."newpoint".$word.$compounds[0];
                if(isset($rownewchoices)){
                    if(isset($_POST[$rownewpoint])&& is_numeric($_POST[$rownewpoint])){
                        $rownewchoices=$rownewchoices."<<>>".$n."[[]]".$compounds[1]."[[]]".$_POST[$rownewpoint]."[[]]".$compounds[3];
                    }else{
                        $rownewchoices=$rownewchoices."<<>>".$n."[[]]".$compounds[1]."[[]]".$compounds[2]."[[]]".$compounds[3];
                    }
                }else{
                    if(isset($_POST[$rownewpoint])&& is_numeric($_POST[$rownewpoint])){
                        $rownewchoices=$n."[[]]".$compounds[1]."[[]]".$_POST[$rownewpoint]."[[]]".$compounds[3];
                    }else{
                        $rownewchoices=$n."[[]]".$compounds[1]."[[]]".$compounds[2]."[[]]".$compounds[3];    
                    }   
                }
                $n=$n+1;
            }
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $sql = "UPDATE $projectname SET $word=:$word WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":$word",$rownewchoices, PDO::PARAM_STR);
            $stmt->bindParam(":id",$rowid, PDO::PARAM_STR);
            $stmt->execute();
        }

        $rowdeleteall=$rowid."delete".$word;
        if(isset($_POST[$rowdeleteall])){
            $choices=explode("<<>>",$row[$word]);
            $n=1;
            foreach($choices as $choice){
                $compounds=explode("[[]]",$choice);
                $rowdelete=$rowid."delete".$word.$compounds[0];
                if(empty($_POST[$rowdelete])){
                    if(isset($rownewchoices)){
                        $rownewchoices=$rownewchoices."<<>>".$n."[[]]".$compounds[1]."[[]]".$compounds[2]."[[]]".$compounds[3];
                    }else{
                        $rownewchoices=$n."[[]]".$compounds[1]."[[]]".$compounds[2]."[[]]".$compounds[3];
                    }
                    $n=$n+1;
                }
            }
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $sql = "UPDATE $projectname SET $word=:$word WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":$word",$rownewchoices, PDO::PARAM_STR);
            $stmt->bindParam(":id",$rowid, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
    
    function create_newquestion($dsn, $user, $password,$projectname){
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        $sql = $pdo -> prepare("INSERT INTO $projectname () VALUES ()");
        $sql -> execute();
    }

    function delete_question($dsn, $user, $password,$projectname){
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        $sql = "SELECT * FROM $projectname";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            $rowiddelete=$row['id']."delete";
            if(isset($_POST[$rowiddelete])){
                $sql = "delete from $projectname where id=:id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $row['id'], PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }

    echo "<form action='' method='post'>";
    echo "<input type='submit' name='newqbutton' value='NewQuestion'>";
    echo "</form>";

    if(isset($_POST['newqbutton'])){
        create_newquestion($dsn, $user, $password,$projectname);
    }else{
        delete_question($dsn, $user, $password,$projectname);
        edit_question($dsn, $user, $password, $projectname);
    }
    show_questions($dsn, $user, $password,$projectname);

    echo "<form action='execute.php' method='POST' target='_blank'>";
    echo "<button onclick='location.href='execute.php''>生成</button>";
    echo "</form>";

    ?>
    
</body>
</html>