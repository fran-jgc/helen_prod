<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/phpreport/interface.ReportData.php");

class MySQLRD_helen implements IReportData {
	
	private $dbhandle;
	private $Result;
	private $RowData;
	private $Command;
	
	private $sp;
	private $param;
	
	public function __construct($command, $sp, $param){
		$this->Command = $command;
		$this->sp = $sp;
		$this->param = $param;
	}

	/**
	 * start data retriving again from the beginning
	 */
	public function reset(){
		$this->dbhandle = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);	
		if (K_TIPO_BD=="oci"){
			if ($this->sp != '') {			
	       		ini_set('max_execution_time', 900); //900 seconds = 15 minutes 
	       		$this->dbhandle->EXECUTE_SP($this->sp, $this->param);
			}
			$this->Result = $this->dbhandle->query($this->Command);
		}
		else if (K_TIPO_BD=="mssql"){
			if ($this->sp != '')			
				$this->Result = $this->dbhandle->query("execute $this->sp $this->param");
			else
				$this->Result = $this->dbhandle->query($this->Command);
		}
	}

	public function getNextRow() {
		return $this->RowData;
	}
	
	public function hasMoreRow() {
		return ($this->RowData = $this->dbhandle->get_row());
	}

}
?>