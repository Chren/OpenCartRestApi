<?php
class ModelToolValidate extends Model {
	public function validateEmail($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	public function validateTelephone($telephone) {
		return preg_match("/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8]))\d{8}$/", $telephone);
	}
}