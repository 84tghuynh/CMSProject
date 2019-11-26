<?php
  session_start();
  if(isset($_SESSION['email']))
  {
    if(isset($_POST['search'])) echo "ehoo00000000000000000000000";
  }else{
     header("Location: login.php");
     exit();
  }
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="utf-8">
    <title>CMS for Roy's Florist</title>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
    <?php include("head.php"); ?>
    <div id="header">
        <div id="header-left">
            <div><img src="img/ninja.png" alt="Florist"></div>
            <div><a href="admin.php"><h1>CMS For Roy's Florist </h1></a></div>
        </div>
        <?php if($_SESSION['roletype']==1): ?>
            <div id="header-right">
                <div><a href="umanage.php">Manage User</a></div>
            </div>
        <?php endif ?>
        <div id="header-right">
            <div><a href="category.php">Category</a></div>
        </div>
        <div id="header-right">
            <div><a href="create.php">New Product</a></div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="content">
        <div id="recent">
            <h2>List of products of all Categories</h2>
        </div>
    </div>
</body>
</html>
