<?php

ob_start();
include __DIR__ . '/../zenzero/content-search.php';
$parent_content = ob_get_clean();

$parent_content = zenzero_aa_remove_author($parent_content);

echo $parent_content;
