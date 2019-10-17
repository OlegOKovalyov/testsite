<?php
namespace Flexi\Core;

class Error extends Notice
{
    public static function showMessage($error)
    {
        if (!is_wp_error($error)) {
            parent::showMessage($error);
            return;
        }

        ob_start(); ?>
        <div class="flexi-message error notice">
            <p><?php _e($error->get_error_message()); ?></p>
        </div>
        <?php

        $html = ob_get_clean();
        echo $html;
    }
}
