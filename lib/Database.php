<?php
 

class Database {
    protected $config;
    private PDO|null $conn;

    public  $table;

    private $stmt;

    private $error;
    public function connect():void
    {
        try{
            $this->conn = new PDO(...$this->config);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
				echo $this->error;
            die;
        }
    }
    public function disconnect():void
    {
        $this->conn = null;
    }
    public function query(string $sql):void
	{

			$this->connect();
			$this->query = $sql;
			$this->stmt = $this->conn->prepare($sql);
		}

    public function bind($param, $value, $type = null){

			// Type Check
			if( is_null($type) ){

				switch (true) {

					case is_int($value):
						$type = PDO:: PARAM_INT;
						break;

					case is_bool($value):
						$type = PDO:: PARAM_BOOL;
						break;

					case is_null($value):
						$type = PDO:: PARAM_NULL;
						break;

					default:
						$type = PDO:: PARAM_STR;

				}

			}

			$this->stmt->bindValue($param, $value, $type);
		}
        public function execute(){
			return $this->stmt->execute();
		}

		/** Get query result set */
		public function resultSet(){
			$this->execute();
			return $this->stmt->fetchAll(PDO::FETCH_OBJ);
		}

		/** Get single query */
		public function resultSingle(){
			$this->execute();
			return $this->stmt->fetch(PDO::FETCH_OBJ);
		}

		/** Get query row count */
		public function rowCount(){

			return $this->stmt->rowCount();
		}
        

}