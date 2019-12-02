<?php
    require('connect.php');
    $category_array = '';
    $select = 'SELECT * FROM category ORDER BY created DESC';
    // A PDO::Statement is prepared from the query.
    $category = $db->prepare($select);
    // Execution on the DB server is delayed until we execute().
    $category->execute();
    $category_array = $category->fetchAll();

 ?>
<form id="search" action="search.php" method="post">
  <div>
    <select id="categorysearch" name="categoryseach">
        <option value="0">All</option>
        <?php foreach($category_array as $cate): ?>
          <option value="<?= $cate['categoryId'] ?>"><?= $cate['name']?></option>
        <?php endforeach ?>
    </select>
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
