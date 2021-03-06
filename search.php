<?php
  require('connect.php');
  session_start();
  $searchInput = '';
  $category_input = '';
  $categoryname  ='';
  $catedetail ='';
  if(isset($_SESSION['email']))
  {
      if
      (
        isset($_POST['search']) &&
        !empty($_POST['searchbox'])                    &&
        strlen(trim($_POST['searchbox']))!=0           &&
        strlen($_POST['searchbox'])>=1
      )
      {

          $searchInput = filter_input(INPUT_POST, 'searchbox', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

          $category_input = filter_input(INPUT_POST, 'categoryseach', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

          $query = '';
          $stmt_product='';
          $stmt='';
          if($category_input ==0)
          {
                $query = 'SELECT * FROM products WHERE productName LIKE \'%'.$searchInput.'%\''.' OR '.'description LIKE  \'%'.$searchInput.'%\'';
                $stmt_product = $db->prepare($query);
                $stmt_product->execute();
                $categoryname = 'All';
          }
          else{
                $querycate = 'SELECT name FROM category WHERE categoryId = :categoryid';
                $stmt = $db->prepare($querycate);
                $stmt->bindValue(':categoryid',$category_input);
                $stmt->execute();
                $catedetail = $stmt->fetch();
                $categoryname = $catedetail['name'];

                $query = 'SELECT * FROM products WHERE categoryId = :categoryid AND  (productName LIKE \'%'.$searchInput.'%\''.' OR '.'description LIKE  \'%'.$searchInput.'%\')';
                $stmt_product = $db->prepare($query);
                $stmt_product->bindValue(':categoryid',$category_input);
                $stmt_product->execute();
          }
          // $stmt_product->bindValue(':searchInput',$searchInput);

          $array_product = $stmt_product->fetchAll();
      }
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
            <div class="header-right">
                <div><a href="umanage.php">Manage User</a></div>
            </div>
        <?php endif ?>
        <div class="header-right">
            <div><a href="category.php">Category</a></div>
        </div>
        <div class="header-right">
            <div><a href="create.php">New Product</a></div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="content">
        <div id="recent">
            <h2>The search results of keyword: <span class="keyword"> <?= $searchInput ?> </span> in category: <span class="keyword"> <?= $categoryname ?> </span></h2>
        </div>
        <?php
        if
        (
          isset($_POST['search'])                        &&
          !empty($_POST['searchbox'])                    &&
          strlen(trim($_POST['searchbox']))!=0           &&
          strlen($_POST['searchbox'])>=1
        ):
        ?>
            <?php if(count($array_product) >0 ): ?>
                <?php foreach($array_product as $product): ?>
                        <div class="product_title">
                            <h3><a href="show.php?id=<?= $product['productId'] ?>"><?= $product['productName'] ?></a></h3>
                            <div class="edit_link"><a href="edit.php?id=<?= $product['productId'] ?>">edit</a></div>
                            <div class="clear"></div>
                        </div>
                <?php endforeach ?>
            <?php else: ?>
                <p> No product matches </p>
            <?php endif ?>

        <?php endif ?>

    </div>
</body>
</html>
