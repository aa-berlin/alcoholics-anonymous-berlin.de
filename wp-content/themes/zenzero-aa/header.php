<?php

ob_start();
include __DIR__ . '/../zenzero/header.php';
$parent_header = ob_get_clean();

ob_start();
if (is_active_sidebar('header_teasers')):
    dynamic_sidebar('header_teasers');
endif;
$header_teasers = ob_get_clean();

ob_start();
if (current_user_can('read_private_pages')):
    ?>
    <div id="zenzero-aa-private-menu" class="zenzero-aa-private-menu">
        <?php wp_nav_menu(array('theme_location' => 'zenzero_aa_private_menu')); ?>
    </div>
<?php
endif;
$private_menu = ob_get_clean();

$parent_header = preg_replace('#</header>#', "$0 $private_menu", $parent_header);
$parent_header = preg_replace('#site-branding.*?</div>#s', "$0 $header_teasers", $parent_header);

echo $parent_header;
