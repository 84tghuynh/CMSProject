<?php
  require('connect.php');
  $searchInput = '';
  $category_input = '';
  $categoryname  ='';
  $catedetail ='';
      if
      (
        isset($_POST['search']) &&
        !empty($_POST['searchbox'])                    &&
        strlen(trim($_POST['searchbox']))!=0           &&
        strlen($_POST['searchbox'])>=1
      )
      {
          // $searchInput = filter_input(INPUT_POST, 'searchbox', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
          // // Query comment
          // $query = 'SELECT * FROM products WHERE productName LIKE \'%'.$searchInput.'%\''.' OR '.'description LIKE  \'%'.$searchInput.'%\'';
          // $stmt_product = $db->prepare($query);
          // // $stmt_product->bindValue(':searchInput',$searchInput);
          // $stmt_product->execute();
          // $array_product = $stmt_product->fetchAll();

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
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="utf-8">
    <title>CMS for Roy's Florist</title>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
    <?php include("uhead.php"); ?>
    <div id="header">
        <div id="header-left">
            <div><img src="img/ninja.png" alt="Florist"></div>
            <div><a href="index.php"><h1>CMS For Roy's Florist </h1></a></div>
        </div>
        <div id="header-right">
            <div><a href="userregister.php">Register</a></div>
        </div>
        <div id="header-right">
            <div><a href="login.php">Login</a></div>
        </div>
        <div id="header-right">
            <div><a href="admin.php">Administrative</a></div>
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
                            <h3><a href="ushow.php?id=<?= $product['productId'] ?>"><?= $product['productName'] ?></a></h3>
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
