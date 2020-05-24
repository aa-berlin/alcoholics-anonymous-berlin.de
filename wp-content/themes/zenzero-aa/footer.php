<?php

ob_start();
include __DIR__ . '/../zenzero/footer.php';
$parent_footer = ob_get_clean();

ob_start();
?>
<nav class="service-nav smallPart">
    <?php wp_nav_menu(array('theme_location' => 'zenzero_aa_service_menu')); ?>
</nav>
<?php
$service_menu = ob_get_clean();

$parent_footer = preg_replace('#</footer#s', "$service_menu $0", $parent_footer);

echo $parent_footer;
