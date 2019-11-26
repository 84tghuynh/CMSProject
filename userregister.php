<?php
    require('common.php');
    $errorFlag = false;
     /**
  * Process POST
  */
  if (
        isset($_POST['registernormaluser'])              &&
        !empty($_POST['password'])                       &&
        strlen(trim($_POST['password']))!=0              &&
        strlen($_POST['password'])>=1                    &&
        !empty($_POST['confirm'])                        &&
        strlen(trim($_POST['confirm']))!=0               &&
        strlen($_POST['confirm'])>=1                     &&
        (validateEmail() == 'validemail' )               &&
        (matchConfirmAndPassword())                      &&
        (!checkExistingUser(trim($_POST['email'])))
    )
    {
        insertNormalUser();

    }else{
        if(isset($_POST['registernormaluser']) && (empty($_POST['password']) || strlen(trim($_POST['password'])) == 0))   $errorFlag = true;
        if(isset($_POST['registernormaluser']) && (empty($_POST['confirm'])  || strlen(trim($_POST['confirm'])) == 0))  $errorFlag = true;
        if(isset($_POST['registernormaluser']) && (validateEmail() != 'validemail' ))  $errorFlag = true;
        if(isset($_POST['registernormaluser']) && !matchConfirmAndPassword() ) $errorFlag = true;
        if(isset($_POST['registernormaluser']) && (checkExistingUser(trim($_POST['email']))) ) $errorFlag = true;
    }
?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="utf-8">
    <title>User Register - CMS for Roy's Florist</title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <script src="js/main.js" type="text/javascript"></script>
</head>
<body>
    <div id="header">
        <div id="header-left">
            <div><img src="img/ninja.png" alt="Florist"></div>
            <div><a href="#"><h1>CMS For Roy's Florist - Register User ! </h1></a></div>
        </div>
        <div id="header-right">
            <div><a href="login.php">Login</a></div>
        </div>
        <div id="header-right">
            <div><a href="index.php">Home</a></div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="content">
        <div class="register">
            <div id="reg-title">
                <h2>Create your CMS Roy's Florist account</h2>
                <p>to continue with Roy's CMS </p>
            </div>
            <form id="registerNormalUserForm" action="userregister.php"   method="post">
                <fieldset id="userInfo">
                <ul>
                    <li>
                        <label for="lastname">Last Name</label>
                        <?php if($errorFlag): ?>
                            <?php if(!empty($_POST['lastname'])): ?>
                                <input id="lastname" name="lastname" type="text" value="<?= $_POST['lastname'] ?>" />
                            <?php else: ?>
                                <input id="lastname" name="lastname" type="text" />
                            <?php endif ?>
                        <?php else: ?>
                                <input id="lastname" name="lastname" type="text" />
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
                                <input id="firstname" name="firstname" type="text" />
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
                                <?php  elseif(checkExistingUser(trim($_POST['email']))): ?>
                                    <input id="email" name="email" type="text" value="<?= trim($_POST['email']) ?>"/>
                                    <p class="registerError error" id="emailformat_error" style="display: block;">* Email address existed</p>
                                <?php  else: ?>
                                    <input id="email" name="email" type="text" value="<?= trim($_POST['email']) ?>"/>
                                    <p class="registerError error" id="email_error">* Required field</p>
                                    <p class="registerError error" id="emailformat_error">* Invalid email address</p>
                                <?php endif ?>
                            <?php else: ?>
                                <input id="email" name="email" type="text" />
                                <p class="registerError error" id="email_error">* Required field</p>
                                <p class="registerError error" id="emailformat_error">* Invalid email address</p>
                            <?php endif ?>
                    </li>
                    <li>
                        <label for="password">Password</label>
                        <?php if($errorFlag): ?>
                                <?php  if((empty($_POST['password'])  || strlen(trim($_POST['password'])) == 0)): ?>
                                    <input id="password" name="password" type="password" />
                                    <p class="registerError error"  id="password_error" style="display: block;">* Required field</p>
                                <?php  else: ?>
                                    <input id="password" name="password" type="password" value="<?=trim($_POST['password'])?>" />
                                    <p class="registerError error"  id="password_error" >* Required field</p>
                                <?php endif ?>
                        <?php else: ?>
                                <input id="password" name="password" type="password" />
                                <p class="registerError error"  id="password_error" >* Required field</p>
                        <?php endif ?>
                    </li>
                    <li>
                        <label for="confirm">Confirm</label>

                        <?php if($errorFlag): ?>
                                <?php  if(isset($_POST['registernormaluser']) && (empty($_POST['confirm'])  || strlen(trim($_POST['confirm'])) == 0)) : ?>
                                    <input id="confirm" name="confirm" type="password" />
                                    <p class="registerError error"  id="confirm_error" style="display: block;">* Required field</p>
                                <?php  elseif(!matchConfirmAndPassword()): ?>
                                    <input id="confirm" name="confirm" type="password"  value="<?=trim($_POST['confirm'])?>"/>
                                    <p class="registerError error" id="confirmmatch_error" style="display: block;">* Not Match Password</p>
                                <?php  else: ?>
                                    <input id="confirm" name="confirm" type="password" value="<?=trim($_POST['confirm'])?>" />
                                    <p class="registerError error"  id="confirm_error" >* Required field</p>
                                    <p class="registerError error" id="confirmmatch_error" >* Not Match Password</p>
                                <?php endif ?>
                        <?php else: ?>
                            <input id="confirm" name="confirm" type="password" />
                            <p class="registerError error"  id="confirm_error" >* Required field</p>
                            <p class="registerError error" id="confirmmatch_error" >* Not Match Password</p>
                        <?php endif ?>
                    </li>

                </ul>
                <div class="clear"></div>
				<p>
                    <!-- <button type="submit" id="submit">Register</button> -->
                    <input type="submit" name="registernormaluser" id="registernormaluser" value="Register">
				</p>
                </fieldset>
            </form>
        </div>
   </div>
</body>
</html>
