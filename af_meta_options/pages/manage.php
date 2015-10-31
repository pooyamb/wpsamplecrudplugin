<?php

/**
 * This function generates view for manage page.
 * @global type $wpdb
 * @param array $messages
 * @param array $rows
 */
function af_meta_manage_view($message, $rows, $count)
{

    ?>
    <div class="wrap">
        <h2>
            <?php echo 'Meta Options'; ?>
            <a class="add-new-h2" href="<?php echo admin_url('admin.php?page=add_meta_options'); ?>">Add New Meta option</a>
        </h2>
        <table class='wp-list-table widefat fixed'>
            <tr><th>Name</th><th>Destination Name</th><th>Value</th><th>Updated On</th><th>&nbsp;</th></tr>
            <?php
            foreach ($rows as $row):

                ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['des_name']; ?></td>
                    <td><?php echo $row['value']; ?></td>
                    <td><?php echo $row['time']; ?></td>
                    <td><a href='<?php echo admin_url('admin.php?page=update_meta_options&id=' . $row['id']); ?>'>Update</a></td>
                </tr>
                <?php
            endforeach;

            ?>
        </table>
        <?php for ($i = 0; $i < $count / 10; $i++): ?>
            <a href='<?php echo admin_url('admin.php?page=manage_meta_options&af_page=' . ($i + 1)); ?>'><?php echo $i + 1; ?></a>
        <?php endfor; ?>
    </div>
    <?php
}
