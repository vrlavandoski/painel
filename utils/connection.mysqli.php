<?php

class ConnectionMysqliFactory {

	private $type;
	private $notify;
	private $connection = null;

	public function __construct($type = "Unknown", $notify = false) {
		$this->type = $type;
		$this->notify = $notify;
	}

	public function connect($host, $user, $pass, $bd, $port = null) {
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

		if ($port === null) {
			$this->connection = new mysqli($host, $user, $pass, $bd);
		} else {
			$this->connection = new mysqli($host, $user, $pass, $bd, $port);
		}

		if ($this->connection->connect_error) {
			throw new Exception($this->connection->connect_error, $this->connection->connect_errno);
		}

		$this->connection->set_charset("utf8");
		$this->onConnectionCreated();
	}

	private function onConnectionCreated() {
		if ($this->notify) {
			error_log("= [" . $this->type . "] Conexao MySQLi criada. - " . $_SERVER["REQUEST_URI"]);
		}
	}

	public function disconnect() {
		if ($this->connection !== null) {
			$this->connection->close();
			$this->connection = null;
		}
	}

	public function getConnection() {
		return $this->connection;
	}

	public function hasConnection() {
		return $this->connection !== null;
	}
}

class ConnectionMysqli {

	protected static $factory = null;
	protected static $factorySanitize = null;

	protected static function getFactory() {
		if (self::$factory == null) {
			self::$factory = new ConnectionMysqliFactory("admin");
		}
		return self::$factory;
	}

	protected static function getFactorySanitize() {
		if (self::$factorySanitize == null) {
			self::$factorySanitize = new ConnectionMysqliFactory("sanitize");
		}
		return self::$factorySanitize;
	}

	public static function getConnection() {
		$factory = self::getFactory();

		if (!$factory->hasConnection()) {
			self::openConnection(HOST, USER, PASS, DB, PORT);
		}

		return $factory->getConnection();
	}

	public static function openConnection($host, $user, $pass, $db, $port) {
		self::closeConnection();
		self::closeConnectionSanitize();
		self::getFactory()->connect($host, $user, $pass, $db, $port);
	}

	public static function closeConnection() {
		self::getFactory()->disconnect();
	}

	function __destruct() {
		self::closeConnection();
		self::closeConnectionSanitize();
	}

	public static function getConnectionSanitize() {
		$factory = self::getFactory();		
		if (!$factory->hasConnection()) {
			if (defined('HOST') && defined('DB')) {
				return self::getConnection();
			} else {
				$factory = self::getFactorySanitize();

				if (!$factory->hasConnection()) {
					$bancoDados = new BancoDados();

					$dadosConexao = $bancoDados->getBancoDados(BANCO_ADMINISTRACAO);
					$port = defined('PORTA_MYSQL') ? PORTA_MYSQL : 3306;

					$factory->connect($dadosConexao["host"], USUARIO_MYSQL, SENHA_MYSQL, $dadosConexao["database_name"], $port);
				}
			}
		}

		return $factory->getConnection();
	}

	public static function closeConnectionSanitize() {
		self::getFactorySanitize()->disconnect();
	}
}



?>