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
    $categoryname='';
    $errorFlag = false;
    
   
    function insertCategory()
    {
        require('connect.php');
        //  Sanitize user input to escape HTML entities and filter out dangerous characters.
        $categoryname = filter_input(INPUT_POST, 'categoryname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

       
        //  Build the parameterized SQL query and bind to the above sanitized values.
        $query = "INSERT INTO category(name) values (:name)";
        $statement = $db->prepare($query);
        
        //  Bind values to the parameters
        $statement->bindValue(':name',$categoryname);
        
        //  Execute the INSERT.
        //  execute() will check for possible SQL injection and remove if necessary

        $flag = $statement->execute();

        if($flag)
        {
            $query = "INSERT INTO categoryhistory(id,categoryid,name,changetype) values (:id,:categoryid,:name,:changetype)";
            $statement = $db->prepare($query);
            //  Bind values to the parameters
            $statement->bindValue(':id',1);
            $statement->bindValue(':categoryid',$db->lastInsertId());
            $statement->bindValue(':name',"Insert");
            $statement->bindValue(':changetype',1);
            $flag = $statement->execute();
        }

        if($flag){
            header("Location: category.php");
            exit();
        }
    }
    session_start(); 
    if(isset($_SESSION['email']))
    {
        // trim() !=0 : user enter space
        if (
                isset($_POST['createcategory'])                      && 
                !empty($_POST['categoryname'])                       && 
                strlen(trim($_POST['categoryname']))!=0              && 
                strlen($_POST['categoryname'])>=1                     
        )
        {
            insertCategory();
        
        }else{
            if(isset($_POST['createcategory']) && (empty($_POST['categoryname']) || strlen(trim($_POST['categoryname'])) == 0))   $errorFlag = true;
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
            <div><a href="admin.php"><h1>CMS for Roy's Florist </h1></a></div>
        </div>
        <div id="header-right">
            <div><a href="category.php">Category</a></div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="content">
        <?php  if($errorFlag) :?>
            <?php if(empty($_POST['categoryname']) || strlen(trim($_POST['categoryname'])) ==0 ):?>
                <p>WARNING: Please type at least one character in Category Nanme</p>
            <?php endif ?>
        <?php endif ?>

        <form id="createcategory" action="categorycreate.php" method="post">
            <div >
                <ol>
                    <li>
                        <label for="categoryname">Category Name</label>
                        <input id="categoryname" name="categoryname" type="text" autofocus />
                       
                    </li>
                </ol>
            </div>
            <div>
                <ol>
                    <li>
                        <input type="submit" name="createcategory" id="smbcreatecategory" value="Create Category">
                    </li>
                </ol>
            </div>
        </form>
    </div>
</body>
</html>
