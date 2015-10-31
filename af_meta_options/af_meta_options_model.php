<?php

/**
 * Model class for Meta options.
 */
class AFMetaModel
{

    /**
     * Stores table name for sql requests.
     * @var type 
     */
    private $table_name = '';

    /**
     * Stores name of database fields
     * @var type 
     */
    public $fields = array('id', 'name', 'des_name', 'update_period', 'value', 'next_turn', 'time');

    /**
     * Stores name of database fields
     * @var type 
     */
    public $required_fields = array('name', 'des_name');

    /**
     * Stores value of the fields after CRUD functions or for them.
     * @var type 
     */
    private $field_values = array();

    /**
     * Constructor function for model
     * @param type $table_name
     */
    public function __construct($table_name)
    {
        $this->table_name = $table_name;
        foreach ($this->fields as $field) {
            $this->field_values[$field] = '';
        }
    }

    /**
     * 
     * @param string $attr_name
     * @return type
     */
    public function __get($attr_name)
    {
        if (isset($this->field_values[$attr_name])) {
            return $this->field_values[$attr_name];
        }
    }

    /**
     * 
     * @param string $attr_name
     * @param string $value
     */
    public function __set($attr_name, $value)
    {
        if (isset($this->field_values[$attr_name])) {
            $this->field_values[$attr_name] = $value;
        }
    }

    /**
     * 
     * @return array
     */
    public function get_field_values()
    {
        return $this->field_values;
    }

    /**
     * This Function will do Update in sql
     * @global type $wpdb
     * @param type $fields
     * @return string
     */
    public function update($fields = array())
    {
        if (!empty($fields)) {
            $this->field_values = array_merge($this->field_values, $fields);
        }
        $message = $this->validate('update');
        if (!$message) {
            global $wpdb;
            if (!$message) {
                $wpdb->update(
                    $this->table_name, array('name' => $this->name,
                    'des_name' => $this->des_name,
                    'value' => $this->value,
                    'update_period' => $this->update_period,
                    'time' => $this->time), array('id' => $this->id), array('%s', '%s', '%s', '%s')
                );
                $message = "Meta Option Updated";
            }
        }
        return $message;
    }

    /**
     * This Function will do delete in sql
     * @global type $wpdb
     * @param type $id
     * @return string
     */
    public function delete($id = null)
    {
        if (!$id && $this->id) {
            $id = $this->id;
        }
        if ($id) {
            global $wpdb;
            $result = $wpdb->delete($this->table_name, array('id' => $id), array('%d'));
            if ($result) {
                $message = 'Item deleted!';
            } else {
                $message = 'Item couldn\'t be deleted';
            }
        } else {
            $message = 'Item couldn\'t be deleted';
        }
        return $message;
    }

    /**
     * This Function will do Add(INSERT) in sql
     * @global type $wpdb
     * @param type $fields
     * @return string
     */
    public function save($fields = array())
    {
        if (!empty($fields)) {
            $this->field_values = array_merge($this->field_values, $fields);
        }
        $message = $this->validate('add');
        if (!$message) {
            global $wpdb;
            if (!$message) {
                $wpdb->insert(
                    $this->table_name, //table
                    array(
                    'name' => $this->name,
                    'des_name' => $this->des_name,
                    'value' => $this->value,
                    'update_period' => $this->update_period,
                    'time' => $this->time
                    ), //data
                    array('%s', '%s', '%s', '%s', '%s') //data format			
                );
                $message = "Meta Option inserted";
            }
        }
        return $message;
    }

    /**
     * 
     * @param array $conditions
     * @return boolean
     */
    public function read_if_exists($conditions = array())
    {
        $row = $this->read('first', $conditions, 1);
        if (empty($row))
            return false;
        else
            return true;
    }

    /**
     * This Function will do "select" in SQL.
     * @global type $wpdb
     * @param type $count
     * @param type $conditions
     * @param type $limit
     * @return type
     */
    public function read($count = 'all', $conditions = array(), $limit = false, $offset = false)
    {
        if (!empty($fields)) {
            $this->field_values = array_merge($this->field_values, $fields);
        }
        global $wpdb;
        $where = '';
        if (is_array($conditions)) {
            $where = 'WHERE ';
            foreach ($conditions as $key => $value) {
                $where .= "`{$key}` = '{$value}' AND ";
            }
            $where.= '1=1 ';
        }
        if ($limit && $count == 'all') {
            $where.="LIMIT {$limit}";
        }
        if ($limit && $offset) {
            $where.=",{$offset}";
        }
        $select_fields = implode(',', $this->fields);
        if ($count == 'first') {
            $rows = $wpdb->get_row("SELECT {$select_fields} from `{$this->table_name}` {$where}", ARRAY_A);
            $this->field_values = $rows;
        } else {
            $rows = $wpdb->get_results("SELECT {$select_fields} from `{$this->table_name}` {$where}", ARRAY_A);
        }
        return $rows;
    }

    /**
     * get count of rows with conditions
     * @global type $wpdb
     * @param array $conditions
     * @return int
     */
    public function count($conditions = array())
    {
        global $wpdb;
        if (is_array($conditions)) {
            $where = 'WHERE ';
            foreach ($conditions as $key => $value) {
                $where .= "`{$key}` = '{$value}' AND ";
            }
            $where.= '1=1 ';
        }
        $rows = $wpdb->get_row("SELECT COUNT(*) from `{$this->table_name}` {$where}", ARRAY_A);
        return $rows['COUNT(*)'];
    }

    /**
     * Validation Function.
     * @global type $wpdb
     * @param type $action
     * @return string
     */
    private function validate($action)
    {
        global $wpdb;
        $message = false;
        foreach ($this->required_fields as $required) {
            if (is_null($this->field_values[$required])) {
                $message[$required] = "{$required} field can't be empty!";
            }
        }

        //if adding a new item, check the name to be unique
        if ($action == 'add') {
            $name_exists = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}"
                . " WHERE `name` = '" . esc_sql($this->name) . "'");
            if ($name_exists > 0)
                $message['name'] = 'This name is not available!';
        }

        //if updating a item, check the name to be still unique
        if ($action == 'update') {
            $name_exists = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}"
                . " WHERE `name` = '" . esc_sql($this->name) . "' AND NOT(`id` = '{$this->id}')");
            if ($name_exists > 0)
                $message['name'] = 'This name is not available!';
        }

        $this->field_values['time'] = date("Y-m-d H:i:s", strtotime($this->field_values['time']));
        return $message;
    }
}
