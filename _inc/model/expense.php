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
class ModelExpense extends Model 
{

	public function getTotalExpense($from=null, $to=null, $store_id = null) 
	{	
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`expenses`.`store_id` = '$store_id' AND `expenses`.`status` = ?";
		if ($from) {
			$where_query .= date_range_expense_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`expenses`.`amount`) as `total` FROM `expenses` 
			WHERE  $where_query");
		$statement->execute(array(1));
		$expense = $statement->fetch(PDO::FETCH_ASSOC);
		return isset($expense['total']) ? $expense['total'] : 0;
	}

	public function getTotalCategoryExpense($category_id, $from, $to, $store_id = null,$returnable='') 
	{	
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`expenses`.`store_id` = ? AND `expenses`.`status` = ? AND `category_id` = ?";
		if ($returnable) {
			$where_query .= " AND `returnable` = '$returnable'";
		}
		if ($from) {
			$where_query .= date_range_expense_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`expenses`.`amount`) as `total` FROM `expenses` 
			WHERE  $where_query");
		$statement->execute(array($store_id,1,$category_id));
		$expense = $statement->fetch(PDO::FETCH_ASSOC);
		return isset($expense['total']) ? $expense['total'] : 0;
	}

	public function getTotalLoss($from, $to, $store_id = null) 
	{	
		$store_id = $store_id ? $store_id : store_id();
		$where_query = "`expenses`.`store_id` = ? AND `expenses`.`status` = ? AND `returnable` = 'no'";
		if ($from) {
			$where_query .= date_range_expense_filter($from, $to);
		}
		$statement = $this->db->prepare("SELECT SUM(`expenses`.`amount`) as `total` FROM `expenses` 
			WHERE  $where_query");
		$statement->execute(array($store_id,1));
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		return isset($row['total']) ? $row['total'] : 0;
	}

	public function getExpensesTotalByCategory($category_id, $from = null, $to = null, $store_id = null) 
{
    $store_id = $store_id ? $store_id : store_id();

    $where_query = "store_id = :store_id AND returnable = 'no' AND status = 1 AND category_id = :category_id";

    if ($from && $to) {
        $where_query .= " AND DATE(fecha_gasto) BETWEEN :from AND :to";
    }

    $sql = "SELECT SUM(amount) AS total FROM expenses WHERE $where_query";

    $stmt = $this->db->prepare($sql);

    $params = [
        ':store_id' => $store_id,
        ':category_id' => $category_id
    ];

    if ($from && $to) {
        $params[':from'] = $from;
        $params[':to'] = $to;
    }

    $stmt->execute($params);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return isset($row['total']) ? $row['total'] : 0;
}

}