<?php

    /**
     *  show.php: displays details of post.
     *  •	Displayed on this page: Post title, timestamp, full post content, edit link
     *  •	The blog displayed is determined by a GET parameter in the URL.
     *      This parameter should be the post's primary key id from the database table.
     *  Validation:
     *      + if the id parameter is not an integer, redirect to admin.php
     *  Author: Giang Truong, Huynh
     *  Updated: Sep 29, 2019
     *
     */
    // require('authenticate.php');
    require('common.php');
    $productdetail ='';
    $id='';
    $productname ='';
    $price = '';
    $image = '';
    $filename ='';
    $ext ='';
    $description ='';
    $created ='';
    $errorFlag = false;



    // $created ='';
    if(isset($_GET['id']) && filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT))
    {
        require('connect.php');
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        $query = 'SELECT * FROM products WHERE productId=:id  LIMIT 1';

        // A PDO::Statement is prepared from the query.
        $statement = $db->prepare($query);
        $statement->bindValue(':id',$id, PDO::PARAM_INT);

        // Execution on the DB server is delayed until we execute().
        $statement->execute();

        if($statement->execute()){
            $productdetail = $statement->fetch();
            $id = $productdetail['productId'];
            $productname =  $productdetail['productName'];
            $price = $productdetail['price'];
            $image = $productdetail['image'];
            $description =  $productdetail['description'];
            $created = $productdetail['created'];

            if($image != '')
            {
                $len = strlen($image);
                $filename = substr($image,0, $len-4);
                $ext = substr($image,$len-3,);
            }

        }

        // Query comment
        $query = 'SELECT * FROM comments WHERE productid=:id ORDER BY created DESC';
        $stmt_comment = $db->prepare($query);
        $stmt_comment->bindValue(':id',$id, PDO::PARAM_INT);
        $stmt_comment->execute();
        $array_comment = $stmt_comment->fetchAll();



    }else{
        // header("Location: admin.php");
        // exit();
    }

     // $created ='';
    if(isset($_POST['id']) && filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT))
    {
         require('connect.php');
         $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

         $query = 'SELECT * FROM products WHERE productId=:id  LIMIT 1';

         // A PDO::Statement is prepared from the query.
         $statement = $db->prepare($query);
         $statement->bindValue(':id',$id, PDO::PARAM_INT);

         // Execution on the DB server is delayed until we execute().
         $statement->execute();

        if($statement->execute()){
             $productdetail = $statement->fetch();
             $id = $productdetail['productId'];
             $productname =  $productdetail['productName'];
             $price = $productdetail['price'];
             $image = $productdetail['image'];
             $description =  $productdetail['description'];
             $created = $productdetail['created'];

             if($image != '')
             {
                 $len = strlen($image);
                 $filename = substr($image,0, $len-4);
                 $ext = substr($image,$len-3,);
             }
        }
        // Query comment
        $query = 'SELECT * FROM comments WHERE productid=:id ORDER BY created DESC';
        $stmt_comment = $db->prepare($query);
        $stmt_comment->bindValue(':id',$id, PDO::PARAM_INT);
        $stmt_comment->execute();
        $array_comment = $stmt_comment->fetchAll();

    }

    /**
     * Process POST
    */
    if (
        isset($_POST['addcomment'])                      &&
        !empty($_POST['username'])                       &&
        strlen(trim($_POST['username']))!=0              &&
        strlen($_POST['username'])>=1                    &&
        !empty($_POST['editcomment'])                    &&
        strlen(trim($_POST['editcomment']))!=0           &&
        strlen($_POST['editcomment'])>=1                 &&
        !empty($_POST['captcha'])                        &&
        strlen(trim($_POST['captcha']))==6
    )
    {
        session_start();
        if(trim($_POST['captcha']) == $_SESSION['captcha'])
            insertComment();
        else $errorFlag = true;
    }else{
        if(isset($_POST['addcomment']) && (empty($_POST['username']) || strlen(trim($_POST['username'])) == 0))   $errorFlag = true;
        if(isset($_POST['addcomment']) && (empty($_POST['editcomment']) || strlen(trim($_POST['editcomment'])) == 0))  $errorFlag = true;
        if(isset($_POST['addcomment']) && (empty($_POST['captcha']) || strlen(trim($_POST['captcha'])) != 6))  $errorFlag = true;
    }
?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="utf-8">
    <title>CMS for Roy's Florist - <?= $productname?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
    <?php include("uhead.php"); ?>
    <div id="header">
        <div id="header-left">
            <div><img src="img/ninja.png" alt="Florist"></div>
            <div><a href="index.php"><h1>CMS for Roy's Florist</h1></a></div>
        </div>
        <div id="header-right">
            <div><a href="admin.php">Administrative</a></div>
        </div>
        <div id="header-right">
            <div><a href="index.php">Home</a></div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="content">
        <div class="product_title">
            <h2><a href="ushow.php?id=<?=$id?>"><?=$productname?></a></h2>
            <div class="clear"></div>
            <small><?=date("M d, Y,  g:i a",strtotime($created))?></small>
        </div>
        <div class='product_content'>
         <?=htmlspecialchars_decode($description)?>
        </div>
        <div>
        <h4> Price: $<?= $price ?> </h4>
        </div>
        <div>
            <?php  if($image != ''): ?>
                <div class='medium'>
                    <img src= "<?='uploads/'.$filename.'_medium.'.$ext  ?>" alt="<?= $filename.'_medium.'.$ext ?>" />
                </div>
                <div class="size">
                    <div class='standard'>
                        <h4>Standard</h4>
                        <img src="<?='uploads/'.$filename.'_standard.'.$ext  ?>" alt="<?= $filename.'_standard.'.$ext ?>" />
                    </div>
                    <div class='deluxe'>
                        <h4>Deluxe</h4>
                        <img src="<?='uploads/'.$filename.'_deluxe.'.$ext  ?>" alt="<?= $filename.'_deluxe.'.$ext ?>" />
                    </div>
                    <div class='premium'>
                        <h4>Premium</h4>
                        <img src="<?='uploads/'.$filename.'_premium.'.$ext  ?>" alt="<?= $filename.'_premium.'.$ext ?>" />
                    </div>
                <div>
            <?php endif ?>
        </div>
    </div>
    <div class="clear"></div>
    <div class="comment">
        <h3>Comments</h3>
        <div>
            <?php foreach($array_comment as $comment): ?>
                <?php if($comment['moderate'] == 0): ?>
                    <div class="comment_thread">
                        <small><?=date("M d, Y,  g:i a",strtotime($comment['created'])) ?></small>
                        <div class="clear"></div>
                        <div class="commment_content"><?= $comment['comment'] ?></div>
                        <h4><?= $comment['name'] ?></a></h4>
                        <hr/>
                    </div>
                <?php endif ?>
            <?php endforeach ?>
        </div>
        <div>
                <?php  if($errorFlag) :?>

                    <?php if(empty($_POST['username']) || strlen(trim($_POST['username'])) ==0 ):?>
                        <p class='warning'>WARNING: Please type at least one character in username</p>
                    <?php endif ?>

                    <?php if(empty($_POST['editcomment']) ||  strlen(trim($_POST['editcomment']))==0):?>
                        <p class='warning'>WARNING: Please type at least one character in comment</p>
                    <?php endif ?>

                <?php endif ?>
        </div>
        <form id="editcomment" action="ushow.php" method="post">
            <div>
                <ol>
                    <li>
                        <?php if(isset($_GET['id'])): ?>
                            <input name="id" type="hidden" value="<?=$id?>"/>
                        <?php endif ?>

                        <?php if(isset($_POST['id'])): ?>
                            <input name="id" type="hidden" value="<?=$_POST['id']?>"/>
                        <?php endif ?>
                    </li>
                    <li>
                        <?php  if($errorFlag) :?>
                            <?php if(!empty($_POST['editcomment'])):?>
                                <p><textarea id="editcomment" name="editcomment"><?= $_POST['editcomment']?></textarea></p>
                            <?php elseif(empty($_POST['editcomment'])): ?>
                                <p><textarea id="editcomment" name="editcomment"></textarea></p>
                            <?php endif ?>
                        <?php else: ?>
                            <p><textarea id="editcomment" name="editcomment"></textarea></p>
                        <?php endif ?>
                    </li>
                    <li>
                        <label for="username">Your name</label>
                        <?php  if($errorFlag) :?>
                            <?php if(!empty($_POST['username'])):?>
                                <input id="username" name="username" type="text" value="<?= $_POST['username']?>" />
                            <?php elseif(empty($_POST['username'])): ?>
                                <input id="username" name="username" type="text" />
                            <?php endif ?>
                        <?php else: ?>
                            <input id="username" name="username" type="text"  />
                        <?php endif ?>
                    </li>
                </ol>
            </div>
            <div>
                <ol>
                    <li>
                        <div>
                            <?php  if($errorFlag) :?>

                                <?php if(empty($_POST['captcha']) ||  strlen(trim($_POST['captcha']))!=6 || (trim($_POST['captcha']) == $_SESSION['captcha']) ):?>
                                    <p class='warning'>WARNING: Please check the captcha</p>
                                <?php endif ?>

                            <?php endif ?>
                        </div>
                        <input type="text" name="captcha" id="captcha" placeholder="Captcha">
                        <img src="captcha/captcha.php" alt="Image created by a PHP script">
                    </li>
                    <div class="clear"></div>
                    <li>
                        <input type="submit" name="addcomment" id="addcomment" value="Add Comment">
                    </li>
                </ol>
            </div>
        </form>
    </div>

</body>
</html>
