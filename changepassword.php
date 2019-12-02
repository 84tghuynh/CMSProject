<?php
    require('common.php');
    $errorFlag = false;
    session_start();
    if(isset($_SESSION['email'])&& ($_SESSION['roletype'] ==1) )
    {
        //Show
        if(isset($_GET['id']) && !filter_input(INPUT_GET,'id', FILTER_VALIDATE_INT)){
            header("Location: umanage.php");
            exit();
        }elseif(isset($_GET['id']) && filter_input(INPUT_GET,'id', FILTER_VALIDATE_INT))
        {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        }

        /**
         * Process POST
         * update
        */
        if (
            isset($_POST['changepassword'])             &&
            !empty($_POST['password'])              &&
            strlen(trim($_POST['password']))!=0     &&
            strlen($_POST['password'])>=1
        )
        {
            changePassword();
        }
        else{
            if(isset($_POST['changepassword']) && (empty($_POST['password']) || strlen(trim($_POST['password'])) == 0))   $errorFlag = true;
            // $errorFlag =true;
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
    <title>Admin Change User's Password - CMS for Roy's Florist</title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <script src="js/main.js" type="text/javascript"></script>
</head>
<body>
    <?php include("head.php"); ?>
    <div id="header">
        <div id="header-left">
            <div><img src="img/ninja.png" alt="Florist"></div>
            <div><a href="#"><h1>CMS For Roy's Florist - Admin Change User's Password ! </h1></a></div>
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
                <h2>Change Password of a CMS Roy's Florist account</h2>
                <p>to continue with Roy's CMS </p>
            </div>
            <form id="adduser" action="changepassword.php"   method="post">
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
                        <label for="password">New Password</label>
                        <?php if($errorFlag): ?>
                                    <input id="password" name="password" type="password" />
                                    <p class="registerError error"  id="password_error" style="display: block;">* Required field</p>
                        <?php else: ?>
                                <input id="password" name="password" type="password" />
                                <p class="registerError error"  id="password_error" >* Required field</p>
                        <?php endif ?>
                    </li>

                </ul>
                <div class="clear"></div>
				<p>
                    <!-- <button type="submit" id="submit">Register</button> -->
                    <input type="submit" name="changepassword" id="changepassword" value="Change Password">
				</p>
                </fieldset>
            </form>
        </div>
   </div>
</body>
</html>
