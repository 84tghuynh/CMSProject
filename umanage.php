<?php

    /**
     * Index.php:
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
    if(isset($_SESSION['email'])&& ($_SESSION['roletype'] ==1) )
    {
        require('connect.php');

        // SQL is written as a String.
        $query = 'SELECT * FROM users ORDER BY created DESC';
        // A PDO::Statement is prepared from the query.
        $statement = $db->prepare($query);
        // Execution on the DB server is delayed until we execute().
        $statement->execute();

        $array_users = $statement->fetchAll();


    }else{
       header("Location: adminlogin.php");
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
            <div><a href="admin.php"><h1>CMS For Roy's Florist </h1></a></div>
        </div>
        <div id="header-right">
            <div><a href="useradd.php">Add User</a></div>
        </div>
        <div id="header-right">
            <div><a href="index.php">Home</a></div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="content">
        <div id="recent">
            <h2>List of all users</h2>
        </div>
         <!-- Fetch each table row in turn. Each $row is a table row hash.
                        Fetch returns FALSE when out of rows, halting the loop. -->
        <table id='tblUser'>
            <thead>
                <tr>
                    <th scope="col">Email</th>
                    <th scope="col">Last name</th>
                    <th scope="col">First Name</th>
                    <th scope="col">Role Name</th>
                    <th scope="col">Created</th>
                    <th scope="col">Modify</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($array_users as $user): ?>
                    <tr>
                        <td><h3><a href="userdetail.php?id=<?= $user['id'] ?>"><?= $user['email'] ?></a></h3></td>
                        <td><?= $user['lastName'] ?></td>
                        <td><?= $user['firstName'] ?></td>
                        <td><?= $user['roleName'] ?></td>
                        <td><?= $user['created'] ?></td>
                        <td>
                            <div class="edit_link"><a href="useredit.php?id=<?= $user['id'] ?>">Edit</a></div>
                            <div class="edit_link"><a href="changepassword.php?id=<?= $user['id'] ?>">Change Password</a></div>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
   </div>
</body>
</html>
