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
<form id="search" action="usearch.php" method="post">
  <div>
    <select id="category" name="categoryseach">
        <option value="0">All</option>
        <?php foreach($category_array as $cate): ?>
          <option value="<?= $cate['categoryId'] ?>"><?= $cate['name']?></option>
        <?php endforeach ?>
    </select>
    <input id="searchbox" name="searchbox" type="text" />
    <input id="searchbutton" name="search" type="submit" value="Search" />
  </div>
</form>
<div class="clear"></div>
