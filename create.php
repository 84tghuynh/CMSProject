<?php

    /**
     * create.php: create a new blog
     * •	Provides a form where the user can enter a new post title and contents.
     * •	The form includes a button for submitting the post to the database.
     * •	This page is protected by HTTP authentication.
     *      Redirect to admin.php after creating blog
     *
     * Valiation:
     * + •	New Posts are validated to ensure the title and content are both at least 1 character in length.
     * + •	All user submitted strings (POSTed titles and blog content) must sanitized
     *      using input_filter and inserted/updated using PDO statements with placeholders bound to values.
     * Author: Giang Truong, Huynh
     * Updated: Sep 29, 2019
     */
    // require('authenticate.php');
    require('upload_filter_origin.php');
    require('common.php');
    $productName='';
    $description='';
    $errorFlag = false;
    $errorFlagForPic = false;

    session_start();
    if(isset($_SESSION['email']))
    {
        /**
         * Begin Processing
         */

        /**
         * Process POST
        */
        if (
                isset($_POST['createproduct'])                      &&
                !empty($_POST['productname'])                       &&
                strlen(trim($_POST['productname']))!=0              &&
                strlen($_POST['productname'])>=1                    &&
                !empty($_POST['description'])                       &&
                strlen(trim($_POST['description']))!=0              &&
                strlen($_POST['description'])>=1                    &&
                filter_var($_POST['price'],FILTER_VALIDATE_FLOAT)   &&
                filter_var($_POST['category'],FILTER_VALIDATE_INT)
            )
        {
            insertProduct($errorFlagForPic);

        }else{
            if(isset($_POST['createproduct']) && (empty($_POST['productname']) || strlen(trim($_POST['productname'])) == 0))   $errorFlag = true;
            if(isset($_POST['createproduct']) && (empty($_POST['description']) || strlen(trim($_POST['description'])) == 0))  $errorFlag = true;
            if(isset($_POST['createproduct']) && (empty($_POST['price']) || !filter_var($_POST['price'],FILTER_VALIDATE_FLOAT)))  $errorFlag = true;
            if(isset($_POST['createproduct']) && !filter_var($_POST['category'],FILTER_VALIDATE_INT))  $errorFlag = true;
        }

        /**
         * Process loading Category to FORM Create Product
         */
        require('connect.php');
        //Loading Category
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
    <?php include("head.php"); ?>
    <div id="header">
        <div id="header-left">
            <div><img src="img/ninja.png" alt="Florist"></div>
            <div><a href="admin.php"><h1>CMS for Roy's Florist </h1></a></div>
        </div>
        <div id="header-right">
            <div><a href="admin.php">Home</a></div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="content">
        <?php  if($errorFlag) :?>

            <?php if(empty($_POST['productname']) || strlen(trim($_POST['productname'])) ==0 ):?>
                <p class='warning'>WARNING: Please type at least one character in Product Nanme</p>
            <?php endif ?>

            <?php if(empty($_POST['description']) ||  strlen(trim($_POST['description']))==0):?>
                <p class='warning'>WARNING: Please type at least one character in Description</p>
            <?php endif ?>

            <?php if(empty($_POST['price']) ||  !filter_var($_POST['price'],FILTER_VALIDATE_FLOAT)):?>
                <p class='warning'>WARNING: Please enter price as a number </p>
            <?php endif ?>

            <?php if(!filter_var($_POST['category'],FILTER_VALIDATE_INT)):?>
                <p class='warning'>WARNING: Please choose a category </p>
            <?php endif ?>

        <?php endif ?>

        <form id="createproduct" action="create.php" method="post" enctype='multipart/form-data'>
            <div >
                <ol>
                    <li>
                        <label for="productname">Product Name</label>
                        <?php  if($errorFlag) :?>
                            <?php if(!empty($_POST['productname'])):?>
                                <input id="productname" name="productname" type="text" value="<?= $_POST['productname']?>" autofocus />
                            <?php elseif(empty($_POST['productname'])): ?>
                                <input id="productname" name="productname" type="text" autofocus />
                            <?php endif ?>
                        <?php else: ?>
                              <?php  if($errorFlagForPic) :?>
                                  <?php if(!empty($_POST['productname'])):?>
                                      <input id="productname" name="productname" type="text" value="<?= $_POST['productname']?>" autofocus />
                                  <?php endif ?>
                              <?php else: ?>
                                    <input id="productname" name="productname" type="text" autofocus />
                              <?php endif ?>
                        <?php endif ?>
                    </li>
                    <li>
                        <label for="description">Description</label>
                        <?php  if($errorFlag) :?>
                            <?php if(!empty($_POST['description'])):?>
                                <p><textarea id="description" name="description"><?= $_POST['description']?></textarea></p>
                            <?php elseif(empty($_POST['description'])): ?>
                                <p><textarea id="description" name="description"></textarea></p>
                            <?php endif ?>
                        <?php else: ?>
                              <?php  if($errorFlagForPic) :?>
                                <?php if(!empty($_POST['description'])):?>
                                    <p><textarea id="description" name="description"><?= $_POST['description']?></textarea></p>
                                <?php endif ?>
                              <?php else: ?>
                                    <p><textarea id="description" name="description"></textarea></p>
                              <?php endif ?>
                        <?php endif ?>
                    </li>
                    <li>
                        <label for="price">Price</label>
                        <?php  if($errorFlag) :?>
                            <?php if(!empty($_POST['price']) && !filter_var($_POST['price'],FILTER_VALIDATE_FLOAT) ):?>
                                <input id="price" name="price" type="text" value="<?= $_POST['price']?>" />
                            <?php elseif(!empty($_POST['price'])): ?>
                                <input id="price" name="price" type="text"  value="<?= $_POST['price']?>" />
                            <?php elseif(empty($_POST['price'])): ?>
                                <input id="price" name="price" type="text" />
                            <?php endif ?>
                        <?php else: ?>
                            <?php  if($errorFlagForPic) :?>
                              <?php if(!empty($_POST['price'])):?>
                                  <input id="price" name="price" type="text"  value="<?= $_POST['price']?>" />
                              <?php endif ?>
                            <?php else: ?>
                                  <input id="price" name="price" type="text"  />
                            <?php endif ?>
                        <?php endif ?>
                    </li>
                    <li>
                      <?php if($errorFlagForPic == true):?>
                          <p class='warning'>WARNING: the file uploaded is not an image </p>
                      <?php endif ?>
                        <label for='image'>Image Filename:</label>
                        <input type='file' name='image' id='image'>
                    </li>
                    <!-- Loading Categories -->
                    <li>
                        <label for='category'>Category:</label>
                        <select id="category" name="category">
                            <option value="">-</option>
                                <?php while($row = $statement->fetch()): ?>
                                    <option value="<?= $row['categoryId'] ?>"><?= $row['name']?></option>
                                <?php endwhile ?>
                        </select>
                    </li>
                </ol>
            </div>
            <div>
                <ol>
                    <li>
                        <input type="submit" name="createproduct" id="smbcreateproduct" value="Create Product">
                    </li>
                </ol>
            </div>
        </form>
    </div>
    <script src="js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
    <script>tinymce.init({selector:'textarea'});</script>
</body>
</html>
