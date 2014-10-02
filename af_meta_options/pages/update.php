<?php

/**
 * This function generates add page view.
 * @global type $wpdb
 * @param type $table_name
 */
function af_meta_update_view($message,$fields){
    ?>
        <div class="wrap">
            <h2>
                Update Meta Option
                <a class="add-new-h2" href="<?php echo admin_url('admin.php?page=update_meta_options&id='.$fields['id'].'&action=delete'); ?>" onclick="if(!window.confirm('are you sure?'))return false">Delete Item</a>
                <a class="add-new-h2" href="<?php echo admin_url('admin.php?page=manage_meta_options'); ?>">Go back to management page</a>
            </h2>
            <?php if (isset($message) && !is_array($message)): ?><div class="updated"><p><?php echo $message;?></p></div><?php endif;?>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <table class='wp-list-table widefat fixed'>
                    <tr>
                        <th>Name</th>
                        <td>
                            <input type="text" name="name" value="<?php echo $fields['name']; ?>" />
                            <?php echo isset($message['name']) ? $message['name'] : '' ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Destination name</th>
                        <td>
                            <input type="text" name="des_name" value="<?php echo $fields['des_name']; ?>" />
                            <?php echo isset($message['des_name']) ? $message['des_name'] : '' ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Value</th>
                        <td>
                            <input type="text" name="value" value="<?php echo $fields['value']; ?>" />
                            <?php echo isset($message['value']) ? $message['value'] : '' ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Update period</th>
                        <td>
                            <input type="time" name="update_period" value="<?php echo $fields['update_period']; ?>" />
                            <?php echo isset($message['update_period']) ? $message['update_period'] : '' ?>
                        </td>
                    </tr>
                </table>
                <input type='submit' name="update" value='Update' class='button'>
            </form>
        </div>
<?php }