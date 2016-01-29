<?php
class ModelOpenapiDevice extends Model {
	public function addDevice($data) {
		$deviceid = md5(uniqid());
		$aeskey = md5($data['uuid'] . uniqid());
		$this->db->query("INSERT INTO `" . DB_PREFIX . "device` SET deviceid = '" . 
			$deviceid . "', uuid = '" . $data['uuid'] . 
			"', aeskey = '" . $aeskey . "', appversion = '" . $this->db->escape($data['appversion']) . 
			"', sysname = '" . $this->db->escape($data['sysname']) . 
			"', sysversion = '" . $this->db->escape($data['sysversion']). "'");
	}

	public function getDeviceByUuid($uuid) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "device` WHERE uuid = '" . $this->db->escape($uuid) . "'");

		return $query->row;
	}

	public function getDeviceByDeviceid($deviceid) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "device` WHERE deviceid = '" . $this->db->escape($deviceid) . "'");
		return $query->row;
	}
}