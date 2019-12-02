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
    if(isset($_SESSION['email']))
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
            <h2>List of products of all Categories</h2>
        </div>
         <!-- Fetch each table row in turn. Each $row is a table row hash.
                        Fetch returns FALSE when out of rows, halting the loop. -->

        <?php foreach($array_category as $category): ?>
            <div class="category_name">
                <h2><a href="categorydetail.php?id=<?= $category['categoryid'] ?>&name=<?= $category['name']?> "><?= $category['name'] ?></a><h2>
            </div>
            <?php foreach($array_product as $product): ?>
                <?php if($category['categoryid'] == $product['categoryid'] ): ?>
                    <div class="product_title">
                        <h3><a href="show.php?id=<?= $product['productid'] ?>"><?= $product['productname'] ?></a></h3>
                        <div class="edit_link"><a href="edit.php?id=<?= $product['productid'] ?>">edit</a></div>
                        <div class="clear"></div>
                        <small><?=date("M d, Y,  g:i a",strtotime($product['created'])) ?></small>
                    </div>
                <?php endif ?>
            <?php endforeach ?>
        <?php endforeach ?>
   </div>
</body>
</html>
