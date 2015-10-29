<?php
/**
 * This function generates view for manage page.
 * @global type $wpdb
 * @param array $messages
 * @param array $rows
 */
function af_meta_manage_view($message,$rows){
?>
    <div class="wrap">
        <h2>
            <?php echo 'Meta Options';?>
            <a class="add-new-h2" href="<?php echo admin_url('admin.php?page=add_meta_options'); ?>">Add New Meta option</a>
        </h2>
        <?php
        echo "<table class='wp-list-table widefat fixed'>";
        echo "<tr><th>Name</th><th>Destination Name</th><th>Value</th><th>Updated On</th><th>&nbsp;</th></tr>";
        foreach ($rows as $row ){
                echo "<tr>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['des_name']}</td>";
                echo "<td>{$row['value']}</td>";	
                echo "<td>{$row['time']}</td>";	
                echo "<td><a href='".admin_url('admin.php?page=update_meta_options&id='.$row['id'])."'>Update</a></td>";
                echo "</tr>";}
        echo "</table>";
        ?>
    </div>
<?php
}