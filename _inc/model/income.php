<?php
/*
| -----------------------------------------------------
| PRODUCT NAME: 	Modern POS
| -----------------------------------------------------
| AUTHOR:			web.ferrocasa.pw
| -----------------------------------------------------
| EMAIL:			info@web.ferrocasa.pw
| -----------------------------------------------------
| COPYRIGHT:		RESERVED BY web.ferrocasa.pw
| -----------------------------------------------------
| WEBSITE:			http://web.ferrocasa.pw
| -----------------------------------------------------
*/
class ModelIncome extends Model 
{
	public function getTotalIncome($from, $to, $store_id = null) 
	{	
		$store_id = $store_id ? $store_id : store_id();

		// Income
		$where_query = "`bank_transaction_price`.`store_id` = '$store_id' AND `transaction_type` IN ('deposit') AND `income_sources`.`type` = 'credit'";
		if ($from) {
			$where_query .= date_range_accounting_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`bank_transaction_price`.`amount`) as `total` FROM `bank_transaction_info` 
			LEFT JOIN `income_sources` ON (`bank_transaction_info`.`source_id` = `income_sources`.`source_id`)
			LEFT JOIN `bank_transaction_price` ON (`bank_transaction_info`.`info_id` = `bank_transaction_price`.`info_id`)
			WHERE  {$where_query}");
		$statement->execute(array());
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		$income = array('total'=>'','group'=>array());
		$income['total'] = isset($row['total']) ? $row['total'] : 0;
		$income['group']=$this->getIncomeGroupType($from, $to, $store_id);
		return $income;
	}

	//jalvarez 12-09-2023
	//se agregan el metodo para obtener los ingresos desglozados por los tipo de pago
	public function getIncomeGroupType($from, $to, $store_id = null){
		$store_id = $store_id ? $store_id : store_id();

		// Income
		$where_query = "sp.invoice_id>0";
		if ($from) {
			$from = date('Y-m-d H:i:s', strtotime($from.' '. '00:00:00')); 
			$to = date('Y-m-d H:i:s', strtotime($to.' '. '23:59:59'));
			$where_query .= " AND si.created_at >= '{$from}' AND si.created_at <= '{$to}'";
		}
		$statement = $this->db->prepare("SELECT (select name from pmethods where pmethod_id=si.pmethod_id) as tipo_pago, 
			SUM(payable_amount) as monto FROM selling_info as si 
			RIGHT JOIN selling_price as sp on sp.invoice_id=si.invoice_id 
			WHERE  {$where_query} 
			GROUP BY pmethod_id");
		$statement->execute(array());
		$row = $statement->fetchAll(PDO::FETCH_ASSOC);
		$income = count($row)>0 ? $row : array();
		return $income;
	}

	public function getTotalSubstractIncome($from, $to, $store_id = null) 
	{	
		$store_id = $store_id ? $store_id : store_id();
		$income = $this->getTotalIncome($from, $to, $store_id);

		// Substract
		$where_query = "`bank_transaction_price`.`store_id` = '$store_id' AND `is_substract` = 1";
		if ($from) {
			$where_query .= date_range_accounting_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`bank_transaction_price`.`amount`) as `total` FROM `bank_transaction_info` 
			LEFT JOIN `bank_transaction_price` ON (`bank_transaction_info`.`info_id` = `bank_transaction_price`.`info_id`)
			WHERE  {$where_query}");
		$statement->execute(array());
		$substract = $statement->fetch(PDO::FETCH_ASSOC);
		$substract = isset($substract['total']) ? $substract['total'] : 0;
		return $income['total'] - $substract;
	}

	public function getTotalSourceIncome($source_id, $from, $to, $store_id = null) 
	{
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`bank_transaction_price`.`store_id` = '$store_id' AND `transaction_type` IN ('deposit') AND `bank_transaction_info`.`source_id` = '{$source_id}' AND `income_sources`.`type` = 'credit'";
		if ($from) {
			$where_query .= date_range_accounting_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`bank_transaction_price`.`amount`) as `total` FROM `bank_transaction_info` 
			LEFT JOIN `income_sources` ON (`bank_transaction_info`.`source_id` = `income_sources`.`source_id`)
			LEFT JOIN `bank_transaction_price` ON (`bank_transaction_info`.`info_id` = `bank_transaction_price`.`info_id`)
			WHERE  {$where_query}");
		$statement->execute(array());
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		$income = isset($row['total']) ? $row['total'] : 0;
		return $income;
	}

	public function getTotalSubstractSourceIncome($source_id, $from, $to, $store_id = null) 
	{	
		$store_id = $store_id ? $store_id : store_id();
		$income = $this->getTotalSourceIncome($source_id, $from, $to, $store_id);

		// Substract
		$where_query = "`bank_transaction_price`.`store_id` = '$store_id' AND `bank_transaction_info`.`source_id` = '{$source_id}' AND `is_substract` = 1";
		if ($from) {
			$where_query .= date_range_accounting_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`bank_transaction_price`.`amount`) as `total` FROM `bank_transaction_info` 
			LEFT JOIN `income_sources` ON (`bank_transaction_info`.`source_id` = `income_sources`.`source_id`)
			LEFT JOIN `bank_transaction_price` ON (`bank_transaction_info`.`info_id` = `bank_transaction_price`.`info_id`)
			WHERE  {$where_query}");
		$statement->execute(array());
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		$substract = isset($row['total']) ? $row['total'] : 0;
		return $income - $substract;
	}

	public function getTotalExpense($from, $to, $store_id = null) 
	{	
		$store_id = $store_id ? $store_id : store_id();
		// $where_query = "`bank_transaction_price`.`store_id` = '$store_id' AND `transaction_type` IN ('withdraw') AND `is_substract` != 1";
		$where_query = "`bank_transaction_price`.`store_id` = '$store_id' AND `transaction_type` IN ('withdraw') AND `bank_transaction_info`.`is_hide` != 1";
		if ($from) {
			$where_query .= date_range_accounting_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`bank_transaction_price`.`amount`) as `total` FROM `bank_transaction_info` 
			LEFT JOIN `expense_categorys` ON (`bank_transaction_info`.`exp_category_id` = `expense_categorys`.`category_id`)
			LEFT JOIN `bank_transaction_price` ON (`bank_transaction_info`.`info_id` = `bank_transaction_price`.`info_id`)
			WHERE  {$where_query}");
		$statement->execute(array());
		$income = $statement->fetch(PDO::FETCH_ASSOC);
		$total = isset($income['total']) ? $income['total'] : 0;
		return $total;
	}

	public function getTotalCategoryExpense($exp_category_id, $from, $to, $store_id = null) 
	{	
		$store_id = $store_id ? $store_id : store_id();
		// $where_query = "`bank_transaction_price`.`store_id` = '$store_id' AND `transaction_type` IN ('withdraw') AND `bank_transaction_info`.`exp_category_id` = '$exp_category_id' AND `is_substract` != 1";
		$where_query = "`bank_transaction_price`.`store_id` = '$store_id' AND `transaction_type` IN ('withdraw') AND `bank_transaction_info`.`exp_category_id` = '$exp_category_id' AND `bank_transaction_info`.`is_hide` != 1";
		if ($from) {
			$where_query .= date_range_accounting_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`bank_transaction_price`.`amount`) as `total` FROM `bank_transaction_info` 
			LEFT JOIN `expense_categorys` ON (`bank_transaction_info`.`exp_category_id` = `expense_categorys`.`category_id`)
			LEFT JOIN `bank_transaction_price` ON (`bank_transaction_info`.`info_id` = `bank_transaction_price`.`info_id`)
			WHERE  {$where_query}");
		$statement->execute(array());
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		return isset($row['total']) ? $row['total'] : 0;
	}

	public function getTotalProfit($from, $to, $store_id = null) 
	{	
		$total = 0;
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`bank_transaction_price`.`store_id` = '$store_id' AND `transaction_type` IN ('deposit') AND `income_sources`.`type` = 'credit' AND `income_sources`.`profitable` = 'yes'";
		if ($from) {
			$where_query .= date_range_accounting_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT `income_sources`.`for_sell`, `bank_transaction_price`.`amount` as `total` FROM `bank_transaction_info` 
			LEFT JOIN `income_sources` ON (`bank_transaction_info`.`source_id` = `income_sources`.`source_id`)
			LEFT JOIN `bank_transaction_price` ON (`bank_transaction_info`.`info_id` = `bank_transaction_price`.`info_id`)
			WHERE  $where_query GROUP BY `income_sources`.`source_id`");
		$statement->execute(array());
		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($rows as $row) {
			if($row['for_sell'] == 1) {
	          $total += get_profit_amount($from,$to);
	        } else {
	        	$total += $row['total'];
	        }
		}
		return $total;
	}
}