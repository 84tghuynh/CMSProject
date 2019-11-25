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
    require('connect.php');
    
    $errorFlag = false;
    $productdetail ='';
    $id='';
    $productname ='';
    $price = '';
    $image = '';
    $filename ='';
    $ext ='';
    $description ='';
    $categoryid ='';

    session_start(); 
    if(isset($_SESSION['email']))
    {
        //Show
        if(isset($_GET['id']) && filter_input(INPUT_GET,'id', FILTER_VALIDATE_INT))
        {
            // require('connect.php');
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

            $query = 'SELECT * FROM products WHERE productid=:id';

            // A PDO::Statement is prepared from the query.
            $statement = $db->prepare($query);
            $statement->bindValue(':id',$id, PDO::PARAM_INT);

            // Execution on the DB server is delayed until we execute().
            if($statement->execute()){
                $productdetail = $statement->fetch();
                $id = $productdetail['productId'];
                $productname =  strip_tags($productdetail['productName'],'<br />');
                $description =  strip_tags($productdetail['description'],'<br />');
                $price = $productdetail['price'];
                $categoryid = $productdetail['categoryId'];
            // $created = $productdetail['created'];

            $image = $productdetail['image'];

                if($image != '')
                {
                    $len = strlen($image);
                    $filename = substr($image,0, $len-4);
                    $ext = substr($image,$len-3,);
                }
            }

            //Loading Category
            //Loading Category
            // SQL is written as a String.
            $query = 'SELECT * FROM category ORDER BY created DESC';

            // A PDO::Statement is prepared from the query.
            $stm_category = $db->prepare($query);

            // Execution on the DB server is delayed until we execute().
            $stm_category->execute();

        }elseif(isset($_GET['id']) && !filter_input(INPUT_GET,'id', FILTER_VALIDATE_INT)){
            header("Location: admin.php");
            exit();
        }

        //Update
        if (isset($_POST['updateblog'])                             && 
            !empty($_POST['editblogtitle'])                         && 
            strlen(trim($_POST['editblogtitle']))!=0                && 
            strlen($_POST['editblogtitle'])>=1                      && 
            !empty($_POST['editblogcontent'])                       && 
            strlen(trim($_POST['editblogcontent']))!=0              && 
            strlen($_POST['editblogcontent'])>=1                    &&
            filter_var($_POST['price'],FILTER_VALIDATE_FLOAT)       &&
            filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)     &&
            filter_var($_POST['category'],FILTER_VALIDATE_INT)

        )
        {
            // require('connect.php');
            //  Sanitize user input to escape HTML entities and filter out dangerous characters.
            $productname = nl2br(htmlspecialchars($_POST['editblogtitle'], ENT_QUOTES, 'UTF-8'));
            $description = nl2br(htmlspecialchars($_POST['editblogcontent'], ENT_QUOTES, 'UTF-8'));
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $price = filter_input(INPUT_POST,'price',FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
            $categoryId = filter_input(INPUT_POST,'category',FILTER_SANITIZE_NUMBER_INT);

            $image_filename ='';

            if (image_upload_detected()) { 
                $image_filename        = $_FILES['image']['name'];
                $temporary_image_path  = $_FILES['image']['tmp_name'];
                $new_image_path        = file_upload_path($image_filename);
                if (file_is_an_image($temporary_image_path, $new_image_path)) {
                    resizeFile($temporary_image_path,$new_image_path);
                }
            }

            $query ='';
            $statement ='';

            if($image_filename ==''){
                //  Build the parameterized SQL query and bind to the above sanitized values.
                $query     = "UPDATE products SET productName = :productname, description = :description, price= :price, categoryId= :categoryid WHERE productId = :id";
                $statement = $db->prepare($query);
            }else{

                $query     = "UPDATE products SET productName = :productname, description = :description, image= :image, price= :price, categoryId= :categoryid WHERE productId = :id";
                $statement = $db->prepare($query);
                $statement->bindValue(':image',$image_filename);
            }
        
        

            $statement->bindValue(':productname', $productname);        
            $statement->bindValue(':description', $description);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':price',$price);
            $statement->bindValue(':categoryid',$categoryId);
            //  Execute the UPDATE
            //  execute() will check for possible SQL injection and remove if necessary

            $flag = $statement->execute();

            if($flag)
            {

                $query = "INSERT INTO changehistory(id,productid,name,changetype) values (:id,:productid,:name,:changetype)";
                $statement = $db->prepare($query);
                    //  Bind values to the parameters
                $statement->bindValue(':id',1);
                $statement->bindValue(':productid',$id);
                $statement->bindValue(':name',"Update");
                $statement->bindValue(':changetype',2);
                $flag = $statement->execute();

            }
            
            if($flag){
                header("Location: show.php?id={$id}");
                exit();
            }
        
        }elseif (isset($_POST['updateblog']) && !filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT))
        {
            header("Location: admin.php");
            exit();
        }else {
            if(isset($_POST['updateblog']) && (empty($_POST['editblogtitle']) || strlen(trim($_POST['editblogtitle'])) ==0 ) )   $errorFlag = true;
            if(isset($_POST['updateblog']) && (empty($_POST['editblogcontent']) || strlen(trim($_POST['editblogcontent'])) ==0 )) $errorFlag = true;
            if(isset($_POST['updateblog']) && (empty($_POST['price']) || !filter_var($_POST['price'],FILTER_VALIDATE_FLOAT)))  $errorFlag = true;
            if(isset($_POST['updateblog']) && !filter_var($_POST['category'],FILTER_VALIDATE_INT))  $errorFlag = true;
        
            if(isset($_POST['updateblog']) && filter_var($_POST['category'],FILTER_VALIDATE_INT))  
                                                $categoryid = filter_input(INPUT_POST,'category',FILTER_SANITIZE_NUMBER_INT);

            // require('connect.php');
            $query = 'SELECT * FROM category ORDER BY created DESC';

            // A PDO::Statement is prepared from the query.
            $stm_category = $db->prepare($query);

            // Execution on the DB server is delayed until we execute().
            $stm_category->execute();
        }

        //Delete
        if (isset($_POST['deleteblog'])  &&  filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT))
        {
            // require('connect.php');
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

            //  Build the parameterized SQL query and bind to the above sanitized values.
            $query     = "DELETE FROM products WHERE productid = :id";
            $statement = $db->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            //  Execute the DELETE
            //  execute() will check for possible SQL injection and remove if necessary
            
            $flag = $statement->execute();

            if($flag)
            {

                $query = "INSERT INTO changehistory(id,productid,name,changetype) values (:id,:productid,:name,:changetype)";
                $statement = $db->prepare($query);
                    //  Bind values to the parameters
                $statement->bindValue(':id',1);
                $statement->bindValue(':productid',$id);
                $statement->bindValue(':name',"Delete");
                $statement->bindValue(':changetype',3);
                $flag = $statement->execute();

            }
            
            if($flag){
                header("Location: admin.php");
                exit();
            }
        
        } elseif (isset($_POST['deleteblog']) && !filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) )
        {
            header("Location: admin.php");
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
    <title>CMS For Roy's Florist - Editing <?=$productname?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <script src="js/main.js" type="text/javascript"></script>
    <script src="js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
    <script>tinymce.init({selector:'textarea'});</script>
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
            <div><a href="admin.php">List Products</a></div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="content">

        <?php  if($errorFlag) :?>
            <?php if(empty($_POST['editblogtitle']) || strlen(trim($_POST['editblogtitle'])) ==0  ):?>
                <p>WARNING: Please type at least one character in Product name</p>
            <?php endif ?>

            <?php if(empty($_POST['editblogcontent']) || strlen(trim($_POST['editblogcontent'])) ==0 ):?>
                <p>WARNING: Please type at least one character in Description</p>
            <?php endif ?>

            <?php if(empty($_POST['price']) ||  !filter_var($_POST['price'],FILTER_VALIDATE_FLOAT)):?>
                <p>WARNING: Please enter price as a number </p>
            <?php endif ?>

            <?php if(!filter_var($_POST['category'],FILTER_VALIDATE_INT)):?>
                <p>WARNING: Please choose a category </p>
            <?php endif ?>

        <?php endif ?>
        <form id="editblog" action="edit.php" method="post" enctype='multipart/form-data'>
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
                        <label for="editblogtitle">Product Name</label>
                        <?php  if($errorFlag) :?>
                            <?php if(!empty($_POST['editblogtitle'])):?>
                                <input id="editblogtitle" name="editblogtitle" type="text" value="<?= $_POST['editblogtitle'] ?>" autofocus /> 
                            <?php elseif(empty($_POST['editblogtitle'])): ?>
                                <input id="editblogtitle" name="editblogtitle" type="text" value="" autofocus />
                            <?php endif ?>
                        <?php else: ?>
                            <?php if($_GET): ?>
                                <input id="editblogtitle" name="editblogtitle" type="text" value="<?=$productname?>" autofocus />
                            <?php endif ?>

                            <?php if(isset($_POST['editblogtitle'])): ?>
                                <input id="editblogtitle" name="editblogtitle" type="text" value="<?=$_POST['editblogtitle']?>" autofocus />
                            <?php endif ?>
                        <?php endif ?>
                    </li>
                    <li>
                        <label for="editblogcontent">Description</label>
                        <?php  if($errorFlag) :?>
                            <?php if(!empty($_POST['editblogcontent'])):?>
                                <p><textarea id="editblogcontent" name="editblogcontent"><?= $_POST['editblogcontent']?></textarea></p>     
                            <?php elseif(empty($_POST['editblogcontent'])): ?>
                                <p><textarea id="editblogcontent" name="editblogcontent"></textarea></p>  
                            <?php endif ?>
                        <?php else: ?>
                            <?php if($_GET): ?>
                                <p><textarea id="editblogcontent" name="editblogcontent"><?=$description?></textarea></p>
                            <?php endif ?>
                           
                            <?php if(isset($_POST['editblogcontent'])): ?>
                                <p><textarea id="editblogcontent" name="editblogcontent"><?=$_POST['editblogcontent']?></textarea></p>
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
                            <?php if($_GET): ?>
                                <input id="price" name="price" type="text" value="<?=$price?>" />
                            <?php endif ?>

                            <?php if(isset($_POST['price'])): ?>
                                <input id="price" name="price" type="text" value="<?=$_POST['price']?>"  />
                            <?php endif ?>
                        <?php endif ?>
                    </li>
                    <li>
                        <label for='image'>Image Filename:</label>
                        <input type='file' name='image' id='image'>
                    </li>
                     <!-- Loading Categories -->
                     <li>
                        <label for='category'>Category:</label>
                        <select id="category" name="category">
                            <option value="">-</option>
                                <?php while($row = $stm_category->fetch()): ?>
                                    <?php if($categoryid == $row['categoryId'] ): ?>
                                        <option value="<?= $row['categoryId'] ?>" selected ><?= $row['name']?></option>
                                    <?php else: ?>
                                        <option value="<?= $row['categoryId'] ?>"><?= $row['name']?></option>
                                    <?php endif ?>
                                <?php endwhile ?>
                        </select>
                    </li>
                </ol>
            </div>
            <?php  if($image != ''): ?>
                <div class='medium'>
                    <img src= "<?='uploads/'.$filename.'_medium.'.$ext  ?>" alt="<?= $filename.'_medium.'.$ext ?>" />
                </div>
            <?php endif ?>
            <div>
                <ol>
                    <li>
                        <input type="submit" name="updateblog" id="sumbitupdate" value="Update Product">
                        <input type="submit" name="deleteblog" id="sumbitdelete" value="Delete">
                    </li>
                </ol>
            </div>
        </form>
    </div>
    <!-- //// -->
    <!-- <script src="js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
    <script>tinymce.init({selector:'textarea'});</script> -->
    <!-- //// -->
</body>
</html>
