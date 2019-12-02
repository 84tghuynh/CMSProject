<?php
    require('common.php');
    $errorFlag = false;


    session_start();
    if(isset($_SESSION['email'])&& ($_SESSION['roletype'] ==1) )
    {
        //Show
        if(isset($_GET['id']) && filter_input(INPUT_GET,'id', FILTER_VALIDATE_INT))
        {
            require('connect.php');
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

            $query = 'SELECT * FROM users WHERE id=:id';

            // A PDO::Statement is prepared from the query.
            $statement = $db->prepare($query);
            $statement->bindValue(':id',$id, PDO::PARAM_INT);

            // Execution on the DB server is delayed until we execute().
            if($statement->execute()){
                $userdetail = $statement->fetch();
                $id = $userdetail['id'];
                $email = $userdetail['email'];
                $lastname = $userdetail['lastName'];
                $_SESSION['old_email'] = $email;
                $firstname = $userdetail['firstName'];
                $roletype = $userdetail['roleType'];
                $roleName = $userdetail['roleName'];
            }
        }elseif(isset($_GET['id']) && !filter_input(INPUT_GET,'id', FILTER_VALIDATE_INT)){
            header("Location: umanage.php");
            exit();
        }

        /**
         * Process POST
         * update
        */
        if (
                isset($_POST['updateuser'])      &&
                (validateEmail() == 'validemail' )
            )
        {

        if( $_SESSION['old_email'] != trim($_POST['email']) )
        {
                if(checkExistingUser(trim($_POST['email'])) )
                {
                    $errorFlag = true;
                }
                else  updateUser();
        }else{
            updateUser();
        }
        }else{
            if(isset($_POST['adduser']) && (validateEmail() != 'validemail' ))  $errorFlag = true;
        }

        /**
         * Process POST
         * delete
        */
        if (
            isset($_POST['deleteuser'])                             &&
            filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)
        )
        {
                require('connect.php');
                $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

                //  Build the parameterized SQL query and bind to the above sanitized values.
                $query     = "DELETE FROM users WHERE id = :id";
                $statement = $db->prepare($query);
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                //  Execute the DELETE
                //  execute() will check for possible SQL injection and remove if necessary

                $flag = $statement->execute();

                if($flag){
                    header("Location: umanage.php");
                    exit();
                }
        }elseif (isset($_POST['deleteuser']) && !filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) )
        {
            header("Location: umanage.php");
            exit();
        }
    }else{
        header("Location: adminlogin.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="utf-8">
    <title>Admin Edit User - CMS for Roy's Florist</title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <script src="js/main.js" type="text/javascript"></script>
</head>
<body>
    <?php include("head.php"); ?>
    <div id="header">
        <div id="header-left">
            <div><img src="img/ninja.png" alt="Florist"></div>
            <div><a href="#"><h1>CMS For Roy's Florist - Admin Edit User ! </h1></a></div>
        </div>
        <div class="header-right">
            <div><a href="login.php">Login</a></div>
        </div>
        <div class="header-right">
            <div><a href="umanage.php">Manage User</a></div>
        </div>
        <div class="header-right">
            <div><a href="index.php">Home</a></div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="content">
        <div class="register">
            <div id="reg-title">
                <h2>Edit a CMS Roy's Florist account</h2>
                <p>to continue with Roy's CMS </p>
            </div>
            <form id="adduser" action="useredit.php"   method="post">
                <fieldset id="userInfo">
                <ul>
                    <li>
                        <?php if(isset($_GET['id'])): ?>
                            <input name="id" type="hidden" value="<?=$id?>"/>
                        <?php endif ?>

                        <?php if(isset($_POST['id'])): ?>
                            <input name="id" type="hidden" value="<?=$_POST['id']?>"/>
                        <?php endif ?>
                    </li>
                    <li>
                        <label for="lastname">Last Name</label>
                        <?php if($errorFlag): ?>
                            <?php if(!empty($_POST['lastname'])): ?>
                                <input id="lastname" name="lastname" type="text" value="<?= $_POST['lastname'] ?>" />
                            <?php else: ?>
                                <input id="lastname" name="lastname" type="text" />
                            <?php endif ?>
                        <?php else: ?>
                                <?php if($_GET): ?>
                                    <input id="lastname" name="lastname" type="text" value="<?=$lastname?>" autofocus />
                                <?php else: ?>
                                    <input id="lastname" name="lastname" type="text" value="<?= $_POST['lastname'] ?>" />
                                <?php endif ?>
                        <?php endif ?>
                    </li>
                    <li>
                        <label for="firstname">First Name</label>
                        <?php if($errorFlag): ?>
                            <?php if(!empty($_POST['firstname'])): ?>
                                <input id="firstname" name="firstname" type="text" value="<?= $_POST['firstname'] ?>" />
                            <?php else: ?>
                                <input id="firstname" name="firstname" type="text" />
                            <?php endif ?>
                        <?php else: ?>
                                <?php if($_GET): ?>
                                    <input id="firstname" name="firstname" type="text" value="<?=$firstname?>" autofocus />
                                <?php else: ?>
                                    <input id="firstname" name="firstname" type="text" value="<?= $_POST['firstname'] ?>" />
                                <?php endif ?>
                        <?php endif ?>
                    </li>
                    <li>
							<label for="email">Email</label>
                            <?php if($errorFlag): ?>
                                <?php if(validateEmail() == "noemail"): ?>
                                    <input id="email" name="email" type="text" />
                                    <p class="registerError error" id="email_error" style="display: block;">* Required field</p>
                               <?php  elseif(validateEmail() == "invalidemail"): ?>
                                    <input id="email" name="email" type="text" value="<?= trim($_POST['email']) ?>"/>
                                    <p class="registerError error" id="emailformat_error" style="display: block;">* Invalid email address</p>
                                <?php  elseif( ($_SESSION['old_email'] != trim($_POST['email']) )&&checkExistingUser(trim($_POST['email']))): ?>
                                    <input id="email" name="email" type="text" value="<?= trim($_POST['email']) ?>"/>
                                    <p class="registerError error" id="emailformat_error" style="display: block;">* Email address existed</p>
                                <?php  else: ?>
                                    <input id="email" name="email" type="text" value="<?= trim($_POST['email']) ?>"/>
                                    <p class="registerError error" id="email_error">* Required field</p>
                                    <p class="registerError error" id="emailformat_error">* Invalid email address</p>
                                <?php endif ?>
                            <?php else: ?>
                                <?php if($_GET): ?>
                                    <input id="email" name="email" type="text"  value="<?=$email?>" autofocus />
                                <?php else: ?>
                                    <input id="email" name="email" type="text" value="<?= trim($_POST['email']) ?>"/>
                                <?php endif ?>

                                <p class="registerError error" id="email_error">* Required field</p>
                                <p class="registerError error" id="emailformat_error">* Invalid email address</p>
                            <?php endif ?>
                    </li>
                    <li>
                        <label for="roletype">User Role</label>
                        <select id="roletype" name="roletype">
                            <?php if($_GET): ?>
                                <?php if($roletype == 0 ): ?>
                                    <option value="0" selected >User</option>
                                <?php else: ?>
                                    <option value="0">User</option>
                                <?php endif ?>

                                <?php if($roletype == 1 ): ?>
                                    <option value="1" selected >Admin</option>
                                <?php else: ?>
                                    <option value="1">Admin</option>
                                <?php endif ?>
                            <?php else: ?>
                                <?php if($_POST['roletype'] == 0 ): ?>
                                    <option value="0" selected >User</option>
                                <?php else: ?>
                                    <option value="0">User</option>
                                <?php endif ?>

                                <?php if($_POST['roletype'] == 1 ): ?>
                                    <option value="1" selected >Admin</option>
                                <?php else: ?>
                                    <option value="1">Admin</option>
                                <?php endif ?>
                            <?php endif ?>
                        </select>
                    </li>
                </ul>
                <div class="clear"></div>
				<p>
                    <input type="submit" name="updateuser" id="updateuser" value="Update User">
                    <input type="submit" name="deleteuser" id="deleteuser" value="Delete User">
				</p>
                </fieldset>
            </form>
        </div>
   </div>
</body>
</html>
