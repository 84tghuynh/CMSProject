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
     // $created ='';
     if(isset($_GET['id']) && filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT))
     {
         require('connect.php');
         $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        //  $name = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT)

        // SQL is written as a String.
        $query = 'SELECT productid, productname, categoryid,created FROM products WHERE categoryId=:id ORDER BY created DESC';

        // A PDO::Statement is prepared from the query.
        $statement = $db->prepare($query);

        $statement->bindValue(':id',$id);
        // Execution on the DB server is delayed until we execute().
        $statement->execute();

        $array_product = $statement->fetchAll();
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
        <div class="header-right">
            <div><a href="admin.php">Administrative</a></div>
        </div>
        <div class="header-right">
            <div><a href="index.php">Home</a></div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="content">
        <div id="recent">
            <h2>List of products of <?= $_GET['name'] ?> category </h2>
        </div>
         <!-- Fetch each table row in turn. Each $row is a table row hash.
                        Fetch returns FALSE when out of rows, halting the loop. -->

            <div class="category_name">
                <h2><span><?= $_GET['name'] ?></span></h2>
            </div>
            <?php foreach($array_product as $product): ?>
                    <div class="product_title">
                        <h3><a href="ushow.php?id=<?= $product['productid'] ?>"><?= $product['productname'] ?></a></h3>
                        <div class="clear"></div>
                        <small><?=date("M d, Y,  g:i a",strtotime($product['created'])) ?></small>
                    </div>
            <?php endforeach ?>
   </div>
</body>
</html>
