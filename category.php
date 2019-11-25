<?php

    /**
     * admin.php: 
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
        $query = 'SELECT * FROM category ORDER BY created DESC';

        // A PDO::Statement is prepared from the query.
        $statement = $db->prepare($query);

        // Execution on the DB server is delayed until we execute().
        $statement->execute(); 
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
    <div class="userinfo">
      <p><strong>User: </strong><?= $_SESSION['email'] ?> - <strong>Role: </strong><?= $_SESSION['rolename'] ?> </p>
    </div>
    <div class="clear"></div>
    <div class="userinfo">
        <div><a href="logout.php"><h4>Logout</h4></a></div>
    </div>
    <div class="clear"></div>
    <div id="header">
        <div id="header-left">
            <div><img src="img/ninja.png" alt="Florist"></div>
            <div><a href="admin.php"><h1>CMS For Roy's Florist </h1></a></div>
        </div>
        
        <div id="header-right">
            <div><a href="categorycreate.php">New Category</a></div>
        </div>
        <div id="header-right">
            <div><a href="admin.php">Home</a></div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="content">
        <div id="recent">
            <h2>List of categories</h2>
        </div>
         <!-- Fetch each table row in turn. Each $row is a table row hash.
                        Fetch returns FALSE when out of rows, halting the loop. -->
        <?php while($row = $statement->fetch()): ?>
            <div class="product_title">
                <h2><a href="categorydetail.php?id=<?= $row['categoryId'] ?>&name=<?= $row['name']?> "><?= $row['name'] ?></a></h2>
                <div class="edit_link"><a href="categoryedit.php?id=<?= $row['categoryId'] ?>">edit</a></div>
                <div class="clear"></div>
                <small><?=date("M d, Y,  g:i a",strtotime($row['created'])) ?></small>  
            </div>
        <?php endwhile ?>
   </div>

</body>
</html>

