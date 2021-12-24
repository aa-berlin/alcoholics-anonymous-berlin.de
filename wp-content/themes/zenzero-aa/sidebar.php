<?php

echo ZENZERO_AA_SIDEBAR_DELIMITER;
?>
<div id="sidebar">
    <div id="secondary" class="widget-area nano">
        <div class="nano-content"></div>
    </div><!-- #secondary -->

    <div class="sidebar-content">
        <?php dynamic_sidebar( 'sidebar-1' ); ?>
    </div>
</div>
<?php
echo ZENZERO_AA_SIDEBAR_DELIMITER;
