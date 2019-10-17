<?php
namespace Flexi\Core;

class Notice
{
    protected $data;

    function __construct($data)
    {
        $this->data = $data;
        if (is_admin()) {
            add_action('admin_notices', [$this, 'showMessages']);
        }
    }

    public static function showMessage($message)
    {
        if (is_wp_error($message)) {
            Error::showMessage($message);
            return;
        }

        ob_start(); ?>
        <div class="flexi-message updated notice">
            <p><?php _e($message); ?></p>
        </div>
        <?php

        $html = ob_get_clean();
        echo $html;
    }

    public function showMessages()
    {
        if (!is_array($this->data)) {
            $this->showMessage($this->data);
            return;
        }

        foreach ($this->data as $error) {
            $this->showMessage($error);
        }
    }
}