<?php
require_once __DIR__ . '/../inc/page.php';

$page = new Page("users", true, true, true);
$page->print_title();

?>

<div class="container">
  <div class="jumbotron">
    <div class="litebans-index litebans-index-main">
      <h1>Placeholder</h1>
    </div>
  </div>
</div>

<?php $page->print_footer(false); ?>
