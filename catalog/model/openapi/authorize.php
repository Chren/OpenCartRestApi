<?php
class ModelOpenapiAuthorize extends Model {
	public function getCustomer($token) {
		// $this->load->model('api/device');
		$this->load->model('account/customer');
		// $device = $this->model_api_device->getDeviceByDeviceid($deviceid);
		// if (!$device) {
		// 	return false;
		// }

		$customer = $this->getCustomerByApiToken($token);
		return $customer;
	}

	public function getDeviceByUuid($uuid) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "device` WHERE uuid = '" . $this->db->escape($uuid) . "'");

		return $query->row;
	}

	public function getDeviceByDeviceid($deviceid) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "device` WHERE deviceid = '" . $this->db->escape($deviceid) . "'");
		return $query->row;
	}

	public function getCustomerByApiToken($apitoken) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE api_token = '" . $this->db->escape($apitoken) . "'");
		return $query->row;
	}

	public function login($telephone, $password) {
		
	}
}