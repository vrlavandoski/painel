<?php
//include_once(SYSTEM_DIR."common/id.mysqli.class.php");

class PersistentMysqli {
	//TMP NOVO
	function saveBancoTmp($id,$entity){
		if($id==0) {
			return $this->createBancoTmp($entity);
		} else {
			return $this->updateBancoTmp($entity);
		}	
	}
	
	private function createBancoTmp($entity){
		$query = $this->insertQuery($entity->getTableName(), $entity->getRecord());
		$id = queryBancoTmpId($query);
		$entity->setRecordValue("id", $id);	
		return $id;
	}
	
	private function updateBancoTmp($entity){
		$query = $this->updateQuery($entity->getTableName(), $entity->getRecord(), $this->getEntityKeyArray($entity) );
		$id =  queryBancoTmpId($query);
		return $id;	
	}
	
	function loadBancoTmp($id, $entity) {		
		$query = $this->selectQuery($entity->getTableName(), array("id"=>$id, "idEmpresa"=>Auth::$idEmpresa));			
		$result = queryBancoTmp($query);
		$entity->setRecord($this->getRecord($result));
 		return $result->num_rows;
	}
	
	function deleteTmp($entity) {		
		$query = $this->deleteQuery($entity->getTableName(), $this->getEntityKeyArray($entity) );
		queryBancoTmp($query);
	}
	
	function loadByKeyTmp($aKeys, $entity) {		
		$query = $this->selectQuery($entity->getTableName(), $aKeys);			
		$result = queryBancoTmp($query);
		$entity->setRecord($this->getRecord($result));
 		return $result->num_rows;
	}
	//TMP NOVO

	
	function saveExtendedAutoTmp($itens) {
 		$query = $this->createExtendInsertQry($itens);
 		return queryBancoTmp($query); 		
 	}
	
	function saveExtendedAuto($itens) {
 		$query = $this->createExtendInsertQry($itens);
 		querySemLogMysqli($query); 		
 	}
 	
	function createExtendInsertQry($itens){
		$entity = $itens[0];
		$table = $entity->getTableName();
		$colunas = array_keys($entity->getRecord());

		$part1 = "insert into `$table` (";
		if (count($colunas)) {
			$part1 .= "`" . implode("`,`", $colunas) . "`";
		}
		$part1 .= ") values ";

		$part2 = "";
		foreach ($itens as $item) {
			if ($part2 != "") {
				$part2 .= ", ";
			}

			$part2 .= " (";
			$valores = "";
			$record = $item->getRecord();

			foreach ($colunas as $key) {
				if ($valores != "") {
					$valores .= ",";
				}

				if (isset($record[$key])) {
					$val = $record[$key];
				} else {
					$val = null;
				}

				$valores .= quote_smart_mysqli($val);
			}

			$part2 .= $valores. " )";
		}

		return $part1 . $part2;
	}

 	function saveAuto($id, $entity) {
		if($id==0) {
			return $this->createAuto($entity);
		} else {
			$this->update($entity);
		}		
	} 
	
	function save($id, $entity) {
		if($id==0) {
			return $this->create($entity);
		} else {
			$this->update($entity);
		}		
	}

	function saveLogAcesso($id, $entity) {
		if($id==0) {
			return $this->createLogAcesso($entity);
		} else {
			$this->update($entity);
		}
	}
	
	function executeQuery($sql){
		$result = query_mysqli($sql);
		return $result;
	}
	
	function load($id, $entity) {		
		$query = $this->selectQuery($entity->getTableName(), array("id"=>$id, "idEmpresa"=>Auth::$idEmpresa));			
		$result = query_mysqli($query);
		$entity->setRecord($this->getRecord($result));
 		return(mysqli_num_rows($result));
	}
	
	function loadById($id, $entity) {
		$query = $this->selectQuery($entity->getTableName(), array("id"=>$id));	
		$result = query_mysqli($query);
		$entity->setRecord($this->getRecord($result));
		return(mysqli_num_rows($result));
	}

	function loadByKey($aKeys, $entity) {		
		$query = $this->selectQuery($entity->getTableName(), $aKeys);			
		$result = query_mysqli($query);
		$entity->setRecord($this->getRecord($result));
 		return(mysqli_num_rows($result));
	}
	
	function loadByIdEmpresa($id, $entity) {
		$query = $this->selectQuery($entity->getTableName(), array("idEmpresa"=>$id));		
		$result = query_mysqli($query);
		$entity->setRecord($this->getRecord($result));
 		return(mysqli_num_rows($result));
	}

	function findByKeys($keys, $entity) {
		$query = $this->selectQuery($entity->getTableName(), $keys);		
		$result = query_mysqli($query);
 		return($result);
	}
		
	function delete($entity) {
		$query = $this->deleteQuery($entity->getTableName(), $this->getEntityKeyArray($entity) );
		query_mysqli($query);
	}
	
	private function update($entity) {	
		if ($entity->getUpdateId()){
			//id::updateId($entity->getId());
		}
		$query = $this->updateQuery($entity->getTableName(), $entity->getRecord(), $this->getEntityKeyArray($entity) );
		query_mysqli($query);		
	}
	
	private function createAuto($entity) {				
		$query = $this->insertQuery($entity->getTableName(), $entity->getRecord());
		querySemLogMysqli($query);
		$id = mysqli_insert_id(ConnectionMysqli::getConnection());
		return $id;
	}
	
	private function create($entity) {
		if($_SESSION['ESPACO_EXCEDIDO']){
			echo "excedeu o espaço";
			die;			
		}			
		$id = IdMysqli::generateId($entity);
        $entity->setRecordValue("id", $id);	
 		$query = $this->insertQuery($entity->getTableName(), $entity->getRecord());		
 		query_mysqli($query);
		return $id;
	}
	
	private function createLogAcesso($entity) {
		$id = IdMysqli::generateId($entity);
		$entity->setRecordValue("id", $id);
		$query = $this->insertQuery($entity->getTableName(), $entity->getRecord());
		query_mysqli($query);
		return $id;
	}
	
	private function insertQuery($table, $record) {

 		$sql  = "insert into `$table` ( `";
 		
		foreach($record as $key => $val) {
			$sql .= $key . "`,`";	
		}
		
		$sql = substr($sql, 0, -2);
		$sql .= ") values ( ";
	
		foreach($record as $key => $val) {
			$sql .= quote_smart_mysqli($val) . "," ;
		}
		$sql = substr($sql, 0, -1) . ")";
         
		return $sql;
 	}
 	
 	private function deleteQuery($table, $criteria) {
 		$sql = "delete from $table" . $this->whereClause($criteria);
 		return $sql;	
 	}
 	
 	private function updateQuery($table, $record, $criteria) {
 		$sql  = "update `$table` set ";

		foreach($record as $key => $val) {
			$sql .= "`$key` = ".quote_smart_mysqli($val).", ";
		}
		
		$sql = substr($sql, 0, -2) . ' '; 
		$sql .= $this->whereClause($criteria);	
		return $sql; 		
 	}
 	
 	private function selectQuery($table, $criteria) {
 		$sql = "select * from `$table` ";
 		$sql .= $this->whereClause($criteria);
 		return $sql;
 	}
 	
 	private function exists($table, $criteria) {
 		$result = query_mysqli("select * from {$table} where $criteria");
 		return(mysqli_num_rows($result));
 	}
 	
 	private function whereClause($keys) {
 		$rslt = " where ";
 		foreach($keys as $key => $val) {
 			$rslt .= " (`$key` = " . quote_smart_mysqli($val) . ") and "; 
 		}
		$rslt = substr($rslt, 0, -4);
 		return $rslt;
 	}
 	
 	private function limitClause($pageNumber, $rowsPerPage) {
		$offset = ($pageNumber - 1) * $rowsPerPage;   
		if(isset($rowsPerPage) && isset($pageNumber)) {
			return " limit {$offset}, {$rowsPerPage} ";
		} elseif(isset($rowsPerPage)) {
			return " limit {$rowsPerPage} ";
		} else {
			return "";
		}
 	}
 	
 	private function getKeyArray($id) {
 		return array("id"=>$id);
 	}
 	
 	private function getEntityKeyArray($entity) {
 		return array("id"=>$entity->getId());
 	}


	private function getRecord($result) {
		$record = mysqli_fetch_assoc($result);
		return $record;
	}

}
	
?>