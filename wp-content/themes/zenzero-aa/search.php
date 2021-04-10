<?php

ob_start();
include __DIR__ . '/../zenzero/search.php';
$parent_content = ob_get_clean();

echo $parent_content;
