<?php

class Model extends Database{

    protected string $_model;

    public $exist = false;

    public $data;

    public $table_ref;

	// Table name of the model
	public $table;

	public  $table_column_query = null;

	// Specify the table columns
	public  $fillable = null;

	private $queryError = false;

     public function __construct()
    {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8";
        $this->config = [($dsn), DB_USER, DB_PASSWORD, [PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_STRINGIFY_FETCHES => FALSE]];
        $this->connect();
        $this->_model = get_class($this);
        $this->table = strtolower($this->_model).'s';
        $this->table_ref = strtolower($this->_model).'_id';
    }

	public function getAll (){
		# Generate array
		$sql = "SELECT * FROM $this->table ";


		$this->query($sql);

		# Save data to global data holder object
		$this->data = $this->resultSet();
		$this->exist = ($this->rowCount() > 0);

		return $this->data;
	}


	public function get(string|array $data)
    {

		# Check table name if set
		$this->checktable_name();

		# Transform to array
		if( !is_array($data) ){
			$data = [ $this->table_ref => $data ];
		}

		# Generate array
		$sql = "SELECT * FROM ". $this->table ." WHERE ";

		# Append conditional
		$count = count($data) - 1;
		$counted = 0;
		foreach ($data as $key => $value) {
			$sql .= "$key = :$key";
			if( $counted < $count ) $sql .= " AND ";
			$counted++;
		}

		# Pass query
		$this->query($sql);

		# Query based on condition
		foreach ($data as $key => $value) {
			$this->bind(":$key", $value);
		}

		# Save data to global data holder object
		$this->data = $this->resultSingle();
		$this->exist = ($this->rowCount() > 0);

		return $this->data;
	}


	public function exist(string $column, string $data):int
    {
		$this->query("SELECT * FROM ". $this->table ." WHERE ". $column ." = :data");
		$this->bind(":data", $data);
		$this->resultSingle();
		return $this->rowCount();
	}


	public function insert(array|null $data = null):array
    {

		# Cast array into an object
		$this->data = (is_null($data)) ? (object)$this->data : (object)$data;

		# Get columns of the current table
		$this->checkTableColumns();

		# Generate query
		$sql = "INSERT INTO `" . $this->table . "` (";
		$sql_end = "";
		foreach($this->fillable as $key => $value) {
			if( isset( $this->data->{$value} ) ){
				$sql .= "`". $value . "`, ";
				$sql_end .= ":" . $value . ',';
			}else{
				unset( $value );
			}
		}
		$sql = rtrim($sql, ', ') . ') VALUES(' . rtrim($sql_end, ',') . ')';

		# Binding parameters
		$this->query($sql);
		foreach ($this->data as $key => $value) {
			$this->bind(":" . $key, $value);
		}
		$this->execute();

		# Get the inserted ID
		$this->query("SELECT LAST_INSERT_ID() AS 'last' FROM " . $this->table);
		$newID = $this->resultSingle()->last;
	
		# Get inserted data
		return $this->get($newID);
	}


	public function update(array|null $data = null, array $where = []):array
    {

		# Replace with passed updating data if exist
		$data = $data ? (object)$data : (object)$this->data;

		# Get valid columns
		$this->checkTableColumns();

		# Prepare update, check for table columns
		$sql = 'UPDATE ' . $this->table . ' SET ';
		foreach ($data as $key => $value) {
			if( in_array($key, $this->fillable) ){
				$sql .= $key . " = :" . $key . ',';
			}
		}

		# Trim invalid characters
		$sql = rtrim($sql, ',');
		$sql .= ' WHERE ';

		# Append conditional
		$count = count($where) - 1;
		$counted = 0;
		foreach ($where as $key => $value) {
			$sql .= "$key = :$key";
			if( $counted < $count ) $sql .= " AND ";
			$counted++;
		}

		$this->query($sql);

		# Bind parameters
		$count = 0;
		foreach($data as $key => $value){
			if( in_array($key, $this->fillable) ){
				$this->bind(":".$key, $value);
			}
		}
		foreach ($where as $key => $value) {
			$this->bind(":".$key, $value);
		}

		$this->execute();

		# Return the updated data
		return $this->get($where[$this->table_ref]);
	}

	public function delete(string|array $condition = null):bool
    {

		# Transform to array
		if( !is_array($condition) ){
			$condition = [ $this->table_ref => $condition ];
		}

		# Build query, bind parameter and execute query
		$sql = "DELETE FROM " . $this->table . " WHERE ";

		# Append conditional
		$count = count($condition) - 1;
		$counted = 0;
		foreach ($condition as $key => $value) {
			$sql .= "$key = :$key";
			if( $counted < $count ) $sql .= " AND ";
			$counted++;
		}

		# Bind parameters
		$this->query($sql);
		foreach ($condition as $key => $value) {
			$this->bind(":" . $key, $value);
		}

		$this->execute();
		return $this->rowCount() > 0;
	}
	 public function getSub($id, $name,$itm=null)
    {
        
        $table =$name=='category'?'categories': $name . 's';
        $table_id = $name . '_id';
        $linked_table = $itm?? $name . '_' . $this->table;
        $sql = "SELECT * FROM $table WHERE $table_id IN ( SELECT $table_id FROM $linked_table WHERE $this->table_ref = '$id' )";
        $this->query($sql);

        $this->data = $this->resultSet();
		$this->exist = ($this->rowCount() > 0);

		return $this->data;
    }

	public function cquery(string $sql, bool|string $wildcard = "*"):void
    {
		$sql = str_replace($wildcard, $this->table_column_query, $sql);
		$this->query($sql);
	}

	# PRIVATE MODEL HELPER
	private function checktable_name():bool
    {
		if( $this->table == null ){
			echo "ERROR@MODEL.PHP: Table not defined";
			exit;
		}else{
			return true;
		}
	}
	private function checkTableColumns():void
    {
		if( $this->fillable == null ){
			$this->err("Column tables not is defined");
		}
	}

	private function err(string $message):void
    {
		if( DB_ERROR )  echo 'ERROR@MODEL.PHP: ' . $message;
		exit;
	}

	private function warn(string $message):void
    {
		if( DB_ERROR )  echo 'WARNING@MODEL.PHP: ' . $message;
	}
	

}