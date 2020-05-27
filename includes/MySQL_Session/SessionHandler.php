<?php
    require_once ($_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/includes/MySQL_Session/Db.class.php');
    // namespace JamieCressey\SessionHandler;
    define("DB_HOST", "10.165.168.15");
	define("DB_USER", "root");
	define("DB_PASS", "ESXpumpkin1");
	define("DB_NAME", "SessionData");

    class MySQLSessionHandler implements \SessionHandlerInterface
    {
        private $savePath;

        /**
        * a database MySQLi connection resource
        * @var resource
        */
        protected $dbConnection;

        /**
        * the name of the DB table which handles the sessions
        * @var string
        */
        protected $dbTable;
        public static function session_start() {
            //		if (self::$instance == null) {
            //			self::$instance = new Session();
            //	  	}
            //	  	return self::$instance;
            $handler = new MySQLSessionHandler();
            $db = new PDOWrapper\Db(DB_HOST, DB_NAME, DB_USER, DB_PASS);
            $handler->setDbConnection($db);
            $handler->setDbTable('new_sessions');
            session_set_save_handler($handler, true);
            session_start();            
        }        
        /**
        * Set db data if no connection is being injected
        * @param   string  $dbHost 
        * @param   string  $dbUser
        * @param   string  $dbPassword
        * @param   string  $dbDatabase
        */ 
        public function setDbDetails($dbHost, $dbUser, $dbPassword, $dbDatabase)
        {
            $this->dbConnection = new mysqli($dbHost, $dbUser, $dbPassword, $dbDatabase);

            if (mysqli_connect_error()) {
                throw new Exception('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
            }
        }

        /**
        * Inject DB connection from outside
        * @param   object  $dbConnection   expects MySQLi object
        */
        public function setDbConnection($dbConnection)
        {
            $this->dbConnection = $dbConnection;
        }

        /**
        * Inject DB connection from outside
        * @param   object  $dbConnection   expects MySQLi object
        */
        public function setDbTable($dbTable)
        {
            $this->dbTable = $dbTable;
        }

        public function open($savePath, $sessionName)
        {
            $limit = time() - (3600 * 24);
            $sql = "DELETE FROM $this->dbTable WHERE timestamp < :ts";
            $params = array(
                "ts" => $limit
            );
            $res=$this->dbConnection->query($sql, $params);
            if ($res == 0) {
                return true;
            } else {
                return false;
            }
            
        }

        public function close()
        {
            $this->dbConnection->CloseConnection();
            return true;
        }

        public function read($id)
        {
            $sql = "SELECT data FROM $this->dbTable WHERE id = :id";
            $params = array(
                "id" => $id
            );
            if ($result = $this->dbConnection->single($sql, $params)) {
                return (string)$result;
            } else {
                return '';
            }
        }

        public function write($id, $data)
        {
            $sql = "REPLACE INTO $this->dbTable (id, data, timestamp) VALUES(:id, :data, :timestamp)";
            $params = array(
                "id" => $id,
                "data" => $data,
                "timestamp" => time()
            );
            if ($this->dbConnection->query($sql, $params)) {
                return true;
            }
            return false;
        }

        public function destroy($id)
        {
            $sql = "DELETE FROM $this->dbTable WHERE id = :id";
            $params = array(
                "id"=>$id
            );
            return $this->dbConnection->query($sql, $params);
        }

        public function gc($maxlifetime)
        {
            $sql = "DELETE FROM $this->dbTable WHERE timestamp < :ts";
            $params = array(
                "ts" => time() - intval($maxlifetime)
            );
            return $this->dbConnection->query($sql, $params);
        }
    }
