<?php
class ModelOpenapiFeedback extends Model {
	public function addFeedback($data) {
		$question_id = md5(uniqid());
		
		if (isset($data['customer_id'])) {
			$customer_id = $data['customer_id'];
		} else {
			$customer_id = "";
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "feedback` SET question_id = '" . 
			$question_id . "', customer_id = '" . $customer_id . 
			"', content = '" . $this->db->escape($data['content']) . "', email = '" . $this->db->escape($data['email']) . "'");
	}
}