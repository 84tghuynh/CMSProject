<?php

    /**
     * edit.php - show/update/delete
     *   Show blog before editing
     *  •	Provides a form where the user can edit a specific post title and contents.
     *  •	The post being edited is determined by a GET parameter in the URL.
     *      This parameter should be the post's primary key id from the database table.
     *  •	The title and content of the post being edited should appear in the form.
     *  •	The form includes a button for updating the post in the database.
     *  •	The form includes a button to delete the current post from the database.
     *
     *  Validation
     *   •	An updated Posts are validated to ensure the title and content are both at least 1 character in length.
     *      Warning: users if title or content is empty
     *   •	All user submitted strings (POSTed titles and blog content) must sanitized
     *      using input_filter and inserted/updated using PDO statements with placeholders bound to values.
     *   •	Redirect
     *          + show.php after update succesfully
     *          + admin.php after delete successully
     *          + admin.php if id is not an integer when update or delete
     *
     *  Author: Giang Truong, Huynh
     *  Updated: Sep 29, 2019
     *
     */


    // require('authenticate.php');
    require('upload_filter_origin.php');

    $errorFlag = false;
    $categorydetail ='';
    $id='';
    $categoryname ='';

    session_start();
    if(isset($_SESSION['email']))
    {
        //Show
        if(isset($_GET['id']) && filter_input(INPUT_GET,'id', FILTER_VALIDATE_INT))
        {
            require('connect.php');
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

            $query = 'SELECT * FROM category WHERE categoryid=:id';

            // A PDO::Statement is prepared from the query.
            $statement = $db->prepare($query);
            $statement->bindValue(':id',$id, PDO::PARAM_INT);

            // Execution on the DB server is delayed until we execute().
            if($statement->execute()){
                $categorydetail = $statement->fetch();
                $id = $categorydetail['categoryId'];
                $categoryname = $categorydetail['name'];
            }
        }elseif(isset($_GET['id']) && !filter_input(INPUT_GET,'id', FILTER_VALIDATE_INT)){
            header("Location: category.php");
            exit();
        }

        //Update
        if (isset($_POST['updatecategory'])                 &&
            !empty($_POST['editcategoryname'])              &&
            strlen(trim($_POST['editcategoryname']))!=0     &&
            strlen($_POST['editcategoryname'])>=1
        )
        {
            require('connect.php');
            //  Sanitize user input to escape HTML entities and filter out dangerous characters.
            $categoryname = nl2br(htmlspecialchars($_POST['editcategoryname'], ENT_QUOTES, 'UTF-8'));
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

            $query ='';
            $statement ='';

            $query     = "UPDATE category SET name = :name WHERE categoryId = :id";
            $statement = $db->prepare($query);

            $statement->bindValue(':name', $categoryname);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            //  Execute the UPDATE
            //  execute() will check for possible SQL injection and remove if necessary
            $flag = $statement->execute();

            if($flag)
            {
                $query = "INSERT INTO categoryhistory(id,categoryid,name,changetype) values (:id,:categoryid,:name,:changetype)";
                $statement = $db->prepare($query);
                    //  Bind values to the parameters
                $statement->bindValue(':id',1);
                $statement->bindValue(':categoryid',$id);
                $statement->bindValue(':name',"Update");
                $statement->bindValue(':changetype',2);
                $flag = $statement->execute();
            }

            if($flag){
                header("Location: category.php");
                exit();
            }

        }elseif (isset($_POST['updatecategory']) && !filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT))
        {
            header("Location: category.php");
            exit();
        }else {
            if(isset($_POST['updatecategory']) && (empty($_POST['editcategoryname']) || strlen(trim($_POST['editcategoryname'])) ==0 ) )   $errorFlag = true;
        }

        //Delete
        if (isset($_POST['deletecategory'])  &&  filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT))
        {
            require('connect.php');
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

            //  Build the parameterized SQL query and bind to the above sanitized values.
            $query     = "DELETE FROM category WHERE categoryid = :id";
            $statement = $db->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            //  Execute the DELETE
            //  execute() will check for possible SQL injection and remove if necessary

            $flag = $statement->execute();

            if($flag)
            {

                $query = "INSERT INTO categoryhistory(id,categoryid,name,changetype) values (:id,:categoryid,:name,:changetype)";
                $statement = $db->prepare($query);
                    //  Bind values to the parameters
                $statement->bindValue(':id',1);
                $statement->bindValue(':categoryid',$id);
                $statement->bindValue(':name',"Delete");
                $statement->bindValue(':changetype',3);
                $flag = $statement->execute();

            }

            if($flag){
                header("Location: category.php");
                exit();
            }

        } elseif (isset($_POST['deletecategory']) && !filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) )
        {
            header("Location: category.php");
            exit();
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
    <title>CMS For Roy's Florist - Category Editing <?=$categoryname?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <script src="js/main.js" type="text/javascript"></script>
</head>
<body>
    <?php include("head.php"); ?>
    <div id="header">
        <div id="header-left">
            <div><img src="img/ninja.png" alt="Florist"></div>
            <div><a href="admin.php"><h1>CMS For Roy's Florist </h1></a></div>
        </div>
        <div id="header-right">
            <div><a href="category.php">List Categories</a></div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="content">
        <?php  if($errorFlag) :?>
            <?php if(empty($_POST['editcategoryname']) || strlen(trim($_POST['editcategoryname'])) ==0  ):?>
                <p>WARNING: Please type at least one character in Category name</p>
            <?php endif ?>
        <?php endif ?>
        <form id="editcategory" action="categoryedit.php" method="post">
            <div >
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
                        <label for="editblogtitle">Category Name</label>
                        <?php  if($errorFlag) :?>
                            <?php if(!empty($_POST['editcategoryname'])):?>
                                <input id="editcategoryname" name="editcategoryname" type="text" value="<?= $_POST['editcategoryname'] ?>" autofocus />
                            <?php elseif(empty($_POST['editcategoryname'])): ?>
                                <input id="editcategoryname" name="editcategoryname" type="text" value="" autofocus />
                            <?php endif ?>
                        <?php else: ?>
                            <?php if($_GET): ?>
                                <input id="editcategoryname" name="editcategoryname" type="text" value="<?=$categoryname?>" autofocus />
                            <?php endif ?>

                            <?php if(isset($_POST['editcategoryname'])): ?>
                                <input id="editcategoryname" name="editcategoryname" type="text" value="<?=$_POST['editcategoryname']?>" autofocus />
                            <?php endif ?>
                        <?php endif ?>
                    </li>
                </ol>
            </div>
            <div>
                <ol>
                    <li>
                        <input type="submit" name="updatecategory" id="sumbitupdate" value="Update Category">
                        <input type="submit" name="deletecategory" id="sumbitdelete" value="Delete">
                    </li>
                </ol>
            </div>
        </form>
    </div>

</body>
</html>
