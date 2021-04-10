<?php

ob_start();
include __DIR__ . '/../zenzero/content-single.php';
$parent_content = ob_get_clean();

$parent_content = zenzero_aa_remove_author($parent_content);
$parent_content = zenzero_aa_remove_author($parent_content);

echo $parent_content;
