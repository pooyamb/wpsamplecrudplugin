<?php
/*
  Plugin Name: Meta Options for wordpress (Auto filled options)
  Description: Auto Filled meta options.
  Author: Pouya Mobasher Behrouz
  Author URI: mailto:pooyamb@gmail.com
  Version: 0.1
 */

define('AFMetaPluginRoot', plugin_dir_path(__FILE__));

class AFMetaOptions
{
    /**
     * Current version.
     *
     * @var float
     */
    private $version = 0.02;

    /**
     * Will contain plugin' table name.
     *
     * @var float
     */
    private $table_name;

    /**
     * This function will return current version of AFMetaOptions plugin.
     *
     * @return float
     */
    public function get_version()
    {
        return $version;
    }

    /**
     * This function will be called when the plugin has been activated.
     * It will call activate_plugin function in setup class.
     */
    public function activate_plugin()
    {
        require_once AFMetaPluginRoot.'af_meta_options_install.php';
        $installor = new AFMetaOptionsSetup($this->version, $this->table_name);
        $installor->activate_plugin();
    }

    /**
     * This function will be called when the plugin has been deactivated.
     * It will call deactivate_plugin function in setup class.
     */
    public function deactivate_plugin()
    {
        require_once AFMetaPluginRoot.'af_meta_options_install.php';
        $installor = new AFMetaOptionsSetup($this->version, $this->table_name);
        $installor->deactivate_plugin();
    }

    /**
     * This function will be called when the plugin has been deactivated.
     * It will call uninstall_plugin function in setup class.
     */
    public function uninstall_plugin()
    {
        require_once AFMetaPluginRoot.'af_meta_options_install.php';
        $installor = new AFMetaOptionsSetup($this->version, $this->table_name);
        $installor->uninstall_plugin();
    }

    /**
     * This function will set table name variable inside class.
     */
    private function set_table_name()
    {
        $this->table_name = 'wp_afmetafields';
    }

    /**
     * This function will register needles for running the plugin.
     */
    public function register()
    {
        //setting table name
        $this->set_table_name();

        //Register activation hook, will call activate_plugin function when plugin activated.
        register_activation_hook(__FILE__, [$this, 'activate_plugin']);

        //Register deactivation hook, will call deactivate_plugin function when plugin deactivated.
        register_deactivation_hook(__FILE__, [$this, 'deactivate_plugin']);

        //Register uninstall hook, will call uninstall_plugin function when plugin uninstalled.
        register_uninstall_hook(__FILE__, ['AFMetaOptions', 'uninstall_plugin']);

        //define menu links
        add_action('admin_menu', [$this, 'meta_options_admin_menu']);
    }

    /**
     * This function will define admin menu items.
     *
     * @return
     */
    public function meta_options_admin_menu()
    {
        //main plugin item in admin menu.
        add_menu_page('Meta Options', 'Meta Options', 'manage_options', 'manage_meta_options', [$this, 'manage_meta_options'], 'dashicons-megaphone');

        //add meta options sub menu
        add_submenu_page('manage_meta_options', 'Add Meta Option', 'Add Meta Option', 'manage_options', 'add_meta_options', [$this, 'add_meta_options']);

        //update sub menu, it's hidden but we need it for links and forms
        add_submenu_page(null, 'Update Meta Options', 'Update Meta Options', 'manage_options', 'update_meta_options', [$this, 'update_meta_options']);
    }

    /**
     * Function for "Manage meta options" page.
     */
    public function manage_meta_options()
    {
        //First we need handling request.
        $message = [];

        //Creating array of values
        require_once AFMetaPluginRoot.'af_meta_options_model.php';
        $model = new AFMetaModel($this->table_name);
        $page = isset($_GET['af_page']) ? esc_sql($_GET['af_page']) : 1;
        $limit = 10;
        $offset = 10 * ($page - 1);
        $fields = $model->read('all', [], $limit, $offset);
        $count = $model->count();

        require_once AFMetaPluginRoot.'pages'.DIRECTORY_SEPARATOR.'manage.php';
        af_meta_manage_view($message, $fields, $count);
    }

    /**
     * Function for "Add meta options" page.
     */
    public function add_meta_options()
    {
        //First we need handling request.
        $message = [];

        //Creating array of values
        $fields['name'] = isset($_POST['name']) ? $_POST['name'] : null;
        $fields['des_name'] = isset($_POST['des_name']) ? $_POST['des_name'] : null;
        $fields['update_period'] = isset($_POST['update_period']) ? $_POST['update_period'] : null;
        $fields['value'] = isset($_POST['value']) ? $_POST['value'] : null;
        $fields['time'] = isset($_POST['time']) ? $_POST['time'] : null;

        // handle post request
        if (isset($_POST['insert'])) {
            require_once AFMetaPluginRoot.'af_meta_options_model.php';
            //Calling model to add row to database
            $model = new AFMetaModel($this->table_name);
            $message = $model->save($fields);
        }

        //add js and css for jquery UI datepicker
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        require_once AFMetaPluginRoot.'pages'.DIRECTORY_SEPARATOR.'add.php';
        af_meta_add_view($message, $fields);
    }

    private function exit_with_message($message)
    {
        require_once AFMetaPluginRoot.'pages'.DIRECTORY_SEPARATOR.'message.php';
        af_meta_show_message($message);

        return false;
    }

    /**
     * Function for "Update meta options" page.
     */
    public function update_meta_options()
    {
        //including model
        require_once AFMetaPluginRoot.'af_meta_options_model.php';

        //messages variable
        $message = [];

        //Extracting ID
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $id = esc_sql($id);

        //searching for ID
        $model = new AFMetaModel($this->table_name);

        //if id is not set or is not valid, show error message.
        if (!$id || !$model->read_if_exists(['id' => $id])) {
            return $this->exit_with_message('Requested Item do not exists!');
        }

        //Creating array of values
        $fields['id'] = $id;
        $fields['name'] = isset($_POST['name']) ? $_POST['name'] : $model->name;
        $fields['des_name'] = isset($_POST['des_name']) ? $_POST['des_name'] : $model->des_name;
        $fields['update_period'] = isset($_POST['update_period']) ? $_POST['update_period'] : $model->update_period;
        $fields['value'] = isset($_POST['value']) ? $_POST['value'] : $model->value;
        $fields['time'] = isset($_POST['time']) ? $_POST['time'] : $model->time;

        //if update form was sent try to update the row.
        if (isset($_POST['update'])) {
            $message = $model->update($fields);
        }
        //if delete request was sent, try to delete the row.
        if (isset($_GET['action']) && $_GET['action'] == 'delete') {
            $message = $model->delete();

            return $this->exit_with_message($message);
        }

        //add js and css for jquery UI datepicker
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        require_once AFMetaPluginRoot.'pages'.DIRECTORY_SEPARATOR.'update.php';
        af_meta_update_view($message, $fields);
    }
}

$afmeta_options = new AFMetaOptions();
$afmeta_options->register();
