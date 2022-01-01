<?php

ob_start();
dynamic_sidebar( 'sidebar-1' );
$sidebar_html = ob_get_clean();

$sidebar_html = str_replace(' href=', ' data-target-url=', $sidebar_html);

echo ZENZERO_AA_SIDEBAR_DELIMITER;
?>
<div id="sidebar">
    <div id="secondary" class="widget-area nano">
        <div class="nano-content"></div>
    </div><!-- #secondary -->

    <div class="sidebar-content">
        <?php echo $sidebar_html ?>
    </div>
</div>
<?php
echo ZENZERO_AA_SIDEBAR_DELIMITER;
