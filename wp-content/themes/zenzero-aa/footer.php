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

$parent_footer = preg_replace('#</footer#', "$service_menu $0", $parent_footer);
$parent_footer = preg_replace('#<div[^>]+open-search[^>]+>.+?</div>#', '', $parent_footer);

// removes global link to parent template, be sure to add back to imprint!
$parent_footer = preg_replace('#(<div[^>]+site-info[^>]+>.*)<span class="sep">.*(</div>[\r\n\s]*<!--\s*\.site-info\s*-->)#s', '$1 $2', $parent_footer);

// remove empty social links
$parent_footer = preg_replace('#<div[^>]+site-social[^>]+>[\r\n\s]*</div>#s', '', $parent_footer);

echo $parent_footer;
