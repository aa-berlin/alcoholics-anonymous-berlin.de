<?php

ob_start();
include __DIR__ . '/../zenzero/single.php';
$parent_content = ob_get_clean();

echo $parent_content;
