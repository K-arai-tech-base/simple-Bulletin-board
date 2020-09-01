<!doctype html>
<html lang = "ja">
<head>
<meta charset = "utf-8">
<title>simple</title>
</head>
<body>


<?php
$dsn = 'mysql:dbname="DATABASE NAME";host=localhost';
$user = 'USER NAME';
$password = 'PASSWORD';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
$sql = "CREATE TABLE IF NOT EXISTS simpleboard"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "dt  DATETIME,"
	. "pas char(9)"
	.");";
$stmt = $pdo->query($sql);

$ntd = array(htmlspecialchars($_POST["name"],ENT_QUOTES),
htmlspecialchars($_POST["text"],ENT_QUOTES),date("Y/m/d H:i:s"),"","");
$button = $_POST["sub"];
$numbers = array("",$_POST["ndelete"],$_POST["nedit"],$_POST["sign"]);
$pass= array(htmlspecialchars($_POST["pass1"],ENT_QUOTES),
htmlspecialchars($_POST["pass2"],ENT_QUOTES),
htmlspecialchars($_POST["pass1"],ENT_QUOTES),"");
$r = strlen($ntd[0])*strlen($ntd[1])*strlen($pass[0]);
$list = array('id','name','comment','dt');

if($button == "send" && $r == true)
{       
    if($numbers[3] != true)
    {
        $sql = $pdo -> prepare("INSERT INTO simpleboard (name, comment, dt, pas) 
        VALUES (:name, :comment, :dt, :pas)");
        $sql -> bindParam(':name', $ntd[0], PDO::PARAM_STR);
        $sql -> bindParam(':comment', $ntd[1], PDO::PARAM_STR);
        $sql -> bindParam(':dt', $ntd[2], PDO::PARAM_STR);
        $sql -> bindParam(':pas', $pass[0], PDO::PARAM_STR);
        $sql -> execute();
    }
    else
    {
        $sql = 'UPDATE simpleboard SET name=:name,comment=:comment,dt=:dt 
        WHERE id=:id AND pas =:pas';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $ntd[0], PDO::PARAM_STR);
        $stmt->bindParam(':comment', $ntd[1], PDO::PARAM_STR);
        $stmt->bindParam(':dt', $ntd[2], PDO::PARAM_STR);
        $stmt->bindParam(':pas', $pass[0], PDO::PARAM_STR);
        $stmt->bindParam(':id', $numbers[3], PDO::PARAM_INT);
        $stmt->execute();
    }
}

if($button == "delete")
{
    $sql = 'delete from simpleboard where id=:id AND pas=:pas';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $numbers[1], PDO::PARAM_INT);
    $stmt->bindParam(':pas', $pass[1], PDO::PARAM_STR);
    $stmt->execute();
}

if($button == "edit")
{

    $sql = 'SELECT * FROM simpleboard where id=:id AND pas=:pas';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $numbers[2], PDO::PARAM_INT);
    $stmt->bindParam(':pas', $pass[2], PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(); 
    foreach ($results as $row)
    {
    	$numbers[0] = $row['id'];
    	$ntd[3] = $row['name'];
    	$ntd[4] = $row['comment'];
    	$pass[3] = $row['pas'];
    }
}

$sql = 'SELECT * FROM simpleboard';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row)
{
    $lin = '';
    foreach($list as $key)
    {
        $lin = $lin.$row[$key].'　';
    }
    echo $lin."<br>";
}


?>
<form action = "" method = "post">
    <input type = "hidden" name = "sign" value = "<?php echo $numbers[0]; ?>"><br>
    名前　　：<input type = "text" name = "name" value = "<?php echo $ntd[3]; ?>"><br>
    コメント：<input type = "text" name = "text" value = "<?php echo $ntd[4]; ?>"><br>
    pass：<input type ="password" name = "pass1" value ="<?php echo $pass[3]; ?>"
    maxlength = "9" ><br>
    <input type = "submit" name = "sub" value = "send"><br>
    削除フォーム：<input type = "number" name = "ndelete" ><br>
    pass：<input type ="password" name = "pass2" maxlength = "9" ><br>
    <input type = "submit" name = "sub" value = "delete"><br>
    編集フォーム：<input type = "number" name = "nedit"><br>
    pass：<input type ="password" name = "pass3" maxlength = "9" ><br>
    <input type = "submit" name ="sub" value = "edit"><br>
</form>


</body>
</html>