<?php



    function matchConfirmAndPassword()
    {

        if(!empty($_POST['password']) && !empty($_POST['confirm']))
        {
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $confirm = filter_input(INPUT_POST, 'confirm',  FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if($password != $confirm)
                return false;
        }

        return true;

    }

    function insertNormalUser()
    {
        require('connect.php');
        //  Sanitize user input to escape HTML entities and filter out dangerous characters.
        $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lastname =  filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $confirm = filter_input(INPUT_POST, 'confirm',  FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        //  Build the parameterized SQL query and bind to the above sanitized values.
        $query = "INSERT INTO users(firstname,lastname,email,password,roletype,rolename) values (:firstname,:lastname,:email,:password,:roletype,:rolename )";
        $statement = $db->prepare($query);

        $password = password_hash($password,PASSWORD_BCRYPT);
        //  Bind values to the parameters
        $statement->bindValue(':firstname',$firstname);
        $statement->bindValue(':lastname',$lastname);
        $statement->bindValue(':email',$email);
        $statement->bindValue(':password',$password);
        $statement->bindValue(':roletype',0);
        $statement->bindValue(':rolename','user');
        //  Execute the INSERT.
        //  execute() will check for possible SQL injection and remove if necessary

        $flag = $statement->execute();

        if($flag){
            header("Location: login.php");
            exit();
        }
    }

    /**
     * Add user: normal or admin
     */
    function addUser()
    {
        require('connect.php');
        //  Sanitize user input to escape HTML entities and filter out dangerous characters.
        $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lastname =  filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $confirm = filter_input(INPUT_POST, 'confirm',  FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $roletype = filter_input(INPUT_POST, 'roletype',  FILTER_SANITIZE_NUMBER_INT);

        //  Build the parameterized SQL query and bind to the above sanitized values.
        $query = "INSERT INTO users(firstname,lastname,email,password,roletype,rolename) values (:firstname,:lastname,:email,:password,:roletype,:rolename )";
        $statement = $db->prepare($query);

        $password = password_hash($password,PASSWORD_BCRYPT);
        //  Bind values to the parameters
        $statement->bindValue(':firstname',$firstname);
        $statement->bindValue(':lastname',$lastname);
        $statement->bindValue(':email',$email);
        $statement->bindValue(':password',$password);
        $statement->bindValue(':roletype', $roletype);

        if($roletype == 1 )
            $statement->bindValue(':rolename','admin');
        else
            $statement->bindValue(':rolename','user');
        //  Execute the INSERT.
        //  execute() will check for possible SQL injection and remove if necessary

        $flag = $statement->execute();

        if($flag){
            header("Location: umanage.php");
            exit();
        }
    }


    /**
     * return true: existing or parameter empty
     *        false: no exist
     */
    function checkExistingUser($email)
    {
        require('connect.php');
        if(!empty($email)){

            $query = 'SELECT email FROM users WHERE email = :email LIMIT 1';
            // A PDO::Statement is prepared from the query.
            $statement = $db->prepare($query);
            $statement->bindValue(':email',$email);
            // Execution on the DB server is delayed until we execute().
            $statement->execute();

            if($statement->rowCount()>0) return true;
            else return false;
        }

        return true;
    }

    /**
     * return true: authentication succeeded
     *        false: authentication failed
     */
    function authenticateUser($email,$password)
    {
        require('connect.php');
        if(!empty($email) && !empty($password) ){

            $query = 'SELECT id,password, roletype,rolename FROM users WHERE email = :email LIMIT 1';
            // A PDO::Statement is prepared from the query.
            $statement = $db->prepare($query);
            $statement->bindValue(':email',$email);
            // Execution on the DB server is delayed until we execute().
            $statement->execute();

            if($statement->rowCount()>0)
            {
                $row = $statement->fetch();
                if(password_verify($password, $row['password']))
                {
                    session_start(); // Fires off a session cookie.
                    $_SESSION['email'] = $email;
                    $_SESSION['roletype'] = $row['roletype'];
                    $_SESSION['rolename'] = $row['rolename'];
                    $_SESSION['id'] = $row['id'];

                    return true;
                }

                else return false;
            }
            else return false;
        }

        return false;
    }

    function updateUser()
    {
        require('connect.php');
        //  Sanitize user input to escape HTML entities and filter out dangerous characters.
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lastname =  filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $roletype = filter_input(INPUT_POST, 'roletype',  FILTER_SANITIZE_NUMBER_INT);

        $query ='';
        $statement ='';

        $query     = "UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email, roletype = :roletype, rolename = :rolename  WHERE id = :id";
        $statement = $db->prepare($query);


        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':firstname', $firstname);
        $statement->bindValue(':lastname', $lastname);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':roletype', $roletype);

        if($roletype == 1 )  $statement->bindValue(':rolename', 'admin');
        else $statement->bindValue(':rolename', 'user');
        //  Execute the UPDATE
        //  execute() will check for possible SQL injection and remove if necessary
        $flag = $statement->execute();

        if($flag){
            header("Location: umanage.php");
            exit();
        }
    }

    function changePassword()
    {
        require('connect.php');
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $query ='';
        $statement ='';

        $query     = "UPDATE users SET  password = :password  WHERE id = :id";

        $statement = $db->prepare($query);

        $password = password_hash($password,PASSWORD_BCRYPT);

        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':password', $password);
        $flag = $statement->execute();

        if($flag){
            header("Location: umanage.php");
            exit();
        }
    }

    function insertComment()
    {
        require('connect.php');
        //  Sanitize user input to escape HTML entities and filter out dangerous characters.
        $productid = filter_input(INPUT_POST,'id',FILTER_SANITIZE_NUMBER_INT);
        $username =  filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $comment = filter_input(INPUT_POST, 'editcomment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        //  Build the parameterized SQL query and bind to the above sanitized values.
        $query = "INSERT INTO comments(productid,userid,name,comment) values (:productid,:userid,:name,:comment )";
        $statement = $db->prepare($query);

        //  Bind values to the parameters
        $statement->bindValue(':productid',$productid);
        $statement->bindValue(':userid',0);
        $statement->bindValue(':name',$username);
        $statement->bindValue(':comment',$comment);

        //  Execute the INSERT.
        //  execute() will check for possible SQL injection and remove if necessary

        $flag = $statement->execute();

        if($flag){
            header("Location: ushow.php?id={$productid}");
            exit();
        }
    }

    function insertCommentWithUserLoggedIn()
    {
        require('connect.php');
        //  Sanitize user input to escape HTML entities and filter out dangerous characters.
        $productid = filter_input(INPUT_POST,'id',FILTER_SANITIZE_NUMBER_INT);
        $username =  $_SESSION['email'];
        $userid = $_SESSION['id'];
        $comment = filter_input(INPUT_POST, 'editcomment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        //  Build the parameterized SQL query and bind to the above sanitized values.
        $query = "INSERT INTO comments(productid,userid,name,comment) values (:productid,:userid,:name,:comment )";
        $statement = $db->prepare($query);

        //  Bind values to the parameters
        $statement->bindValue(':productid',$productid);
        $statement->bindValue(':userid',$userid);
        $statement->bindValue(':name',$username);
        $statement->bindValue(':comment',$comment);

        //  Execute the INSERT.
        //  execute() will check for possible SQL injection and remove if necessary

        $flag = $statement->execute();

        if($flag){
            header("Location: show.php?id={$productid}");
            exit();
        }
    }

    function deleteComment($commentid,$productid)
    {
        require('connect.php');
        $query     = "DELETE FROM comments WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $commentid, PDO::PARAM_INT);
        //  Execute the DELETE
        //  execute() will check for possible SQL injection and remove if necessary

        $flag = $statement->execute();

        if($flag){
            header("Location: show.php?id={$productid}");
            exit();
        }
    }

    function updateModerate($commentid,$productid,$moderate)
    {
      require('connect.php');
      $query ='';
      $statement ='';

      $query     = "UPDATE comments SET moderate = :moderate, created=created  WHERE id = :id";
      $statement = $db->prepare($query);

      // echo "commentid: ".$commentid." productid: ".$productid;
      $statement->bindValue(':id', $commentid, PDO::PARAM_INT);
      $statement->bindValue(':moderate', $moderate);

      //  Execute the UPDATE
      //  execute() will check for possible SQL injection and remove if necessary
      $flag = $statement->execute();

      if($flag){
          header("Location: show.php?id={$productid}");
          exit();
      }

    }

    /**
     * $errorFlagForPic = false : no file uploaded or file uploaded is an image
     * $errorFlagForPic = true:  file uploaded is not an image. Nothing happens
    */
    function insertProduct(&$errorFlagForPic)
    {
        require('connect.php');
        //  Sanitize user input to escape HTML entities and filter out dangerous characters.
        $productName = filter_input(INPUT_POST, 'productname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $description =  nl2br(htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8'));
        $price = filter_input(INPUT_POST,'price',FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
        $categoryId = filter_input(INPUT_POST,'category',FILTER_SANITIZE_NUMBER_INT);

        $image_filename ='';

        if (image_upload_detected()) {
            $image_filename        = $_FILES['image']['name'];
            $temporary_image_path  = $_FILES['image']['tmp_name'];
            $new_image_path        = file_upload_path($image_filename);
            if (file_is_an_image($temporary_image_path, $new_image_path)) {
                resizeFile($temporary_image_path,$new_image_path);
            }else
            {
              $image_filename ='';
              $errorFlagForPic = true;
            }
        }

        if($errorFlagForPic == false)
        {
            //  Build the parameterized SQL query and bind to the above sanitized values.
            $query = "INSERT INTO products(productname,price,image,description,categoryid) values (:productname,:price,:image,:description,:categoryid)";
            $statement = $db->prepare($query);

            //  Bind values to the parameters
            $statement->bindValue(':productname',$productName);
            $statement->bindValue(':price',$price);
            $statement->bindValue(':image',$image_filename);
            $statement->bindValue(':description',$description);
            $statement->bindValue(':categoryid',$categoryId);
            //  Execute the INSERT.
            //  execute() will check for possible SQL injection and remove if necessary

            $flag = $statement->execute();

            if($flag)
            {

                $query = "INSERT INTO changehistory(id,productid,name,changetype) values (:id,:productid,:name,:changetype)";
                $statement = $db->prepare($query);
                    //  Bind values to the parameters
                $statement->bindValue(':id',1);
                $statement->bindValue(':productid',$db->lastInsertId());
                $statement->bindValue(':name',"Insert");
                $statement->bindValue(':changetype',1);
                $flag = $statement->execute();

            }

            if($flag){
                header("Location: admin.php");
                exit();
            }
        }
    }

    function insertChangeHistory($id,$productid,$name,$changetype,$flag)
    {
        require('connect.php');
         //  Build the parameterized SQL query and bind to the above sanitized values.
        $query = "INSERT INTO changehistory(id,productid,name,changetype) values (:id,:productid,:name,:changetype)";
        $statement = $db->prepare($query);
            //  Bind values to the parameters
        $statement->bindValue(':id',$id);
        $statement->bindValue(':productid',$productid);
        $statement->bindValue(':name',$name);
        $statement->bindValue(':changetype',$changeType);
        $flag = $statement->execute();
    }


    function updateProduct(&$errorFlagForPic)
    {
        require('connect.php');
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
            }else
            {
              $image_filename ='';
              $errorFlagForPic = true;
            }
        }

        if($errorFlagForPic == false)
        {
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
        }

    }

    /**
   *   Does validate an email address
   * 	return  "noemail" : email does not provide
   *      "invalidemail" : email is invalid
   *        "validemail" : email is valid
   *
   */
   function validateEmail()
   {
       // Validate Email Addess
       // http://emailregex.com

        $patternEmail='/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';

       if(!isset($_POST['email']))
       {
           return "noemail";
       }
       else{
           if(empty($_POST['email']))
           {
               return "noemail";
           }
           else{
                   if(preg_match($patternEmail,trim($_POST['email'])))
                   {
                       return "validemail";
                   }
                   else return "invalidemail";
               }
       }
   }
?>
