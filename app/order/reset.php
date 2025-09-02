<?php
include '../_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    // (1) Delete orders (and items). Reset auto increment
    $_db->query('
       DELETE FROM item;
       DELETE FROM `order`;
       ALTER TABLE `order` AUTO_INCREMENT = 1;
    ');
    temp('info', 'Order and Item tables reset');
}

redirect('history.php');

// ----------------------------------------------------------------------------
