<?php

/**
 * Setup class, this class will handle activation, deactivation and uninstallation orders.
 */
class AFMetaOptionsSetup
{
    /**
     * @var float
     */
    private $version = 0.01;

    /**
     * @var float
     */
    private $table_name;

    /**
     * Constructor functions, sets version number and target table name.
     *
     * @param float  $version
     * @param string $table_name
     */
    public function __construct($version, $table_name)
    {
        $this->version = $version;
        $this->table_name = $table_name;
    }

    /**
     * This function will return current version of AFMeraOptions plugin.
     *
     * @return float
     */
    public function get_version()
    {
        return $version;
    }

    /**
     * This function will be called when the plugin has been activated.
     * it will setup database and check if the app needs creating or updating anything.
     */
    public function activate_plugin()
    {
        //check version and do install
        $optionversion = get_option('AFMetaOptionsVersion');
        if ($optionversion && $optionversion == $this->version) {
            //no need to do anything for now!
        } elseif ($optionversion) {
            $this->upgrade_plugin();
        } else {
            $this->install_plugin();
            add_option('AFMetaOptionsVersion', $this->version);
        }

        //set activate flag in options
        if (get_option('AFMetaOptionsActivate')) {
            update_option('AFMetaOptionsActivate', 1);
        } else {
            add_option('AFMetaOptionsActivate', 1);
        }
    }

    /**
     * This function will be called when the plugin needs installation.
     * It will create database and other needles.
     */
    private function install_plugin()
    {
        //creating the database table.
        global $wpdb;

        $charset_collate = '';

        if (!empty($wpdb->charset)) {
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        }

        if (!empty($wpdb->collate)) {
            $charset_collate .= " COLLATE {$wpdb->collate}";
        }

        $sql = "CREATE TABLE `{$this->table_name}` (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
        		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                name tinytext NOT NULL,
        		value varchar(350) NOT NULL,
                des_name tinytext DEFAULT '' NULL,
                update_period time DEFAULT '00:30:00' NULL,
                next_turn datetime DEFAULT '0000-00-00 00:00:00' NULL,
                UNIQUE KEY id (id)
            ) {$charset_collate};";

        require_once ABSPATH.'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * This function will be called when the plugin needs upgrade.
     */
    private function upgrade_plugin()
    {
    }

    /**
     * This function will be called when the plugin has been deactivated.
     */
    public function deactivate_plugin()
    {
        update_option('AFMetaOptionsActivate', 0);
    }

    /**
     * This function will be called when the plugin has been deactivated.
     */
    public function uninstall_plugin()
    {
        //if uninstall not called from WordPress exit
        if (!defined('WP_UNINSTALL_PLUGIN')) {
            exit();
        }
        delete_option('AFMetaOptionsActivate');
        delete_option('AFMetaOptionsVersion');
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$this->table_name}");
    }
}
