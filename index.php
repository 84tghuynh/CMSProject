<?php

    /**
     * Index.php:
     *           + query database gets 5 most recent blog posts displayed in reverse chronological order.
     *           + For each of these posts you should display: Title, Timestamp, Content
     *           + •	Blog post titles link to full page for each post.
     *           + •	If blog content is larger than 200 character the displayed content is truncated to 200 characters.
     *           + •	If the content is truncated a "Read Full Post" link should be displayed after the content.
     *           + •	An edit link is displayed for each post.
     *           + •	A "New Post" link
     * Author: Giang Truong, Huynh
     * Updated: Sep 29, 2019
     *
     */
    // require('authenticate.php');
    session_start();
    if(!isset($_SESSION['email']))
    {
        require('connect.php');

        // SQL is written as a String.
        $query = 'SELECT productid, productname, categoryid,created FROM products ORDER BY created DESC';
        // A PDO::Statement is prepared from the query.
        $statement = $db->prepare($query);
        // Execution on the DB server is delayed until we execute().
        $statement->execute();

        $array_product = $statement->fetchAll();

        // SQL is written as a String.
        $query = 'SELECT categoryid,name FROM category ORDER BY created DESC';
        // A PDO::Statement is prepared from the query.
        $stm_category = $db->prepare($query);
        // Execution on the DB server is delayed until we execute().
        $stm_category->execute();
        $array_category = $stm_category->fetchAll();

    }else{
        header("Location: admin.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="utf-8">
    <title>CMS for Roy's Florist</title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <script src="js/main.js" ></script>
</head>
<body>
    <?php include("uhead.php"); ?>
    <div id="header">
        <div id="header-left">
            <div><img src="img/ninja.png" alt="Florist"></div>
            <div><a href="index.php"><h1>CMS For Roy's Florist </h1></a></div>
        </div>
        <div class="header-right">
            <div><a href="userregister.php">Register</a></div>
        </div>
        <div class="header-right">
            <div><a href="login.php">Login</a></div>
        </div>
        <div class="header-right">
            <div><a href="admin.php">Administrative</a></div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="content">
        <div id="recent">
            <h2>List of products of all Categories</h2>
        </div>
         <!-- Fetch each table row in turn. Each $row is a table row hash.
                        Fetch returns FALSE when out of rows, halting the loop. -->
        <div class="toogleAll" >
          <strong><span>Show All</span></strong>
        </div>
        <?php foreach($array_category as $category): ?>
          <div id="<?= $category['categoryid'] ?>" class="category">
            <div class="category_name">
                <?php $categoryid =  $category['categoryid']; ?>
                <h2><a href="#"><span><?= $category['name']?></span></a></h2>
            </div>
            <?php foreach($array_product as $product): ?>
                <?php if($categoryid == $product['categoryid'] ): ?>
                    <div class="product_title  toogle <?= $category['categoryid'] ?>">
                        <h3><a href="ushow.php?id=<?= $product['productid'] ?>"><?= $product['productname'] ?></a></h3>
                        <div class="clear"></div>
                        <small><?=date("M d, Y,  g:i a",strtotime($product['created'])) ?></small>
                    </div>
                <?php endif ?>
            <?php endforeach ?>
          </div>
        <?php endforeach ?>
   </div>
</body>
</html>
