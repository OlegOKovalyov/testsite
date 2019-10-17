<?php

namespace Flexi\Core;

use Flexi\Constants;

class Core
{
    public static function init()
    {
        (new self)->coreSetup();
    }

    private function coreSetup()
    {
        $dbSetupOption = getCoreConstant('DB_SETUP_OPTION');
        if (get_option($dbSetupOption)) {
            return;
        }

        add_action('after_switch_theme', [$this, 'setupDatabase']);
    }

    public function setupDatabase()
    {
        global $wpdb;

        try {
            $this->fixDatabaseTimestampFields($wpdb);
            $this->setDatabaseRelations($wpdb);
            $dbSetupOption = getCoreConstant('DB_SETUP_OPTION');
            update_option($dbSetupOption, true);
        } catch (\Exception $e) {
            set_exception_handler(['Core', 'handleException']);
        }
    }

    private function fixDatabaseTimestampFields($wpdb)
    {
        $prefix = $wpdb->prefix;
        $wpdb->query("ALTER TABLE `{$prefix}comments` CHANGE `comment_date_gmt` `comment_date_gmt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE `comment_date` `comment_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
        $wpdb->query("ALTER TABLE `{$prefix}users` CHANGE `user_registered` `user_registered` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
        $wpdb->query("ALTER TABLE `{$prefix}links` CHANGE `link_updated` `link_updated` DATETIME NULL DEFAULT NULL");
        $wpdb->query("ALTER TABLE `{$prefix}posts` CHANGE `post_date` `post_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE `post_date_gmt` `post_date_gmt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE `post_modified` `post_modified` DATETIME NULL DEFAULT NULL, CHANGE `post_modified_gmt` `post_modified_gmt` DATETIME NULL DEFAULT NULL");
    }

    private function setDatabaseRelations($wpdb)
    {
        $prefix = $wpdb->prefix;
        $wpdb->query("ALTER TABLE `{$prefix}usermeta` ADD CONSTRAINT `fk_usermeta_users` FOREIGN KEY (`user_id`) REFERENCES `{$prefix}users`(`ID`) ON DELETE CASCADE ON UPDATE RESTRICT");
        $wpdb->query("ALTER TABLE `{$prefix}termmeta` ADD CONSTRAINT `fk_termmeta_terms` FOREIGN KEY (`term_id`) REFERENCES `{$prefix}terms`(`term_id`) ON DELETE CASCADE ON UPDATE RESTRICT");
        $wpdb->query("ALTER TABLE `{$prefix}commentmeta` ADD CONSTRAINT `fk_commentmeta_comments` FOREIGN KEY (`comment_id`) REFERENCES `{$prefix}comments`(`comment_ID`) ON DELETE CASCADE ON UPDATE RESTRICT");
        $wpdb->query("ALTER TABLE `{$prefix}comments` ADD  CONSTRAINT `fk_comments_posts` FOREIGN KEY (`comment_post_ID`) REFERENCES `{$prefix}posts`(`ID`) ON DELETE CASCADE ON UPDATE RESTRICT");
        $wpdb->query("ALTER TABLE `{$prefix}postmeta` ADD CONSTRAINT `fk_postmetaposts` FOREIGN KEY (`post_id`) REFERENCES `{$prefix}posts`(`ID`) ON DELETE CASCADE ON UPDATE RESTRICT");
        $wpdb->query("ALTER TABLE `{$prefix}term_relationships` ADD CONSTRAINT `fk_term_relationships_posts` FOREIGN KEY (`object_id`) REFERENCES `{$prefix}posts`(`ID`) ON DELETE CASCADE ON UPDATE RESTRICT");
        $wpdb->query("ALTER TABLE `{$prefix}term_relationships` ADD CONSTRAINT `fk_term_relationships_term_taxonomy` FOREIGN KEY (`term_taxonomy_id`) REFERENCES `{$prefix}term_taxonomy`(`term_taxonomy_id`) ON DELETE CASCADE ON UPDATE RESTRICT");
        $wpdb->query("ALTER TABLE `{$prefix}term_taxonomy` ADD CONSTRAINT `fk_term_taxonomy_terms` FOREIGN KEY (`term_id`) REFERENCES `{$prefix}terms`(`term_id`) ON DELETE CASCADE ON UPDATE RESTRICT");
    }

    public function handleException(\Throwable $e)
    {
        Error::showMessage($e);
    }
}
