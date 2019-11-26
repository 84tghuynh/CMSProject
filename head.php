<form id="search" action="search.php" method="post">
  <div>
     <input id="searchbox" name="searchbox" type="text" />
     <input id="searchbutton" name="search" type="submit" value="Search" />
  </div>
</form>
<div class="userinfo">
  <p><strong>User: </strong><?= $_SESSION['email'] ?> - <strong>Role: </strong><?= $_SESSION['rolename'] ?> </p>
</div>
<div class="clear"></div>
<div class="userinfo">
    <div><a href="logout.php"><h4>Logout</h4></a></div>
</div>
<div class="clear"></div>
