<?php


class ControllerOpenapiAccount extends Controller {

	private $debugIt = false;
	
	public function registerdevice() {
		$this->load->model('openapi/device');
		$this->load->language('error/api_error');
		$json = array(
			'code'  => 200,
			'message'  => "",
			'data' =>array(),
		);

		// $this->response->addHeader('Content-Type: application/json');

		$device = $this->model_openapi_device->getDeviceByUuid($this->request->post['uuid']);

		if ($device) {
			$json["data"]["deviceid"] = $device["deviceid"];
			$json["data"]["aeskey"] = $device["aeskey"];
			$json["message"] = "success";
		} else {
			$data = array();
			
			if (isset($this->request->post['appversion'])) {
				$data["appversion"] = $this->request->post['appversion'];
			} else {
				$data["appversion"] = "";
			}

			if (isset($this->request->post['uuid'])) {
				$data["uuid"] = $this->request->post['uuid'];
			} else {
				$data["uuid"] = "";
			}
			
			if (isset($this->request->post['devicetype'])) {
				$data["devicetype"] = (int)$this->request->post['devicetype'];
			} else {
				$data["devicetype"] = 0;
			}

			if (isset($this->request->post['sysname'])) {
				$data["sysname"] = $this->request->post['sysname'];
			} else {
				$data["sysname"] = "";
			}

			if (isset($this->request->post['sysversion'])) {
				$data["sysversion"] = $this->request->post['sysversion'];
			} else {
				$data["sysversion"] = "";
			}

			$this->model_openapi_device->addDevice($data);
			$device = $this->model_openapi_device->getDeviceByUuid($this->request->post['uuid']);
			if (!$device) {
				$json["code"] = 500;
				$json["message"] = $this->language->get('api_error_register_device');
			} else {
				$json["message"] = "success";
				$json["data"]["deviceid"] = $device["deviceid"];
				$json["data"]["aeskey"] = $device["aeskey"];
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function register() {
		require_once(DIR_SYSTEM . 'library/RNCryptor/autoload.php');
		$this->load->model('openapi/device');

		$this->load->language('error/api_error');
		$json = array(
			'code'  => 200,
			'message'  => "",
			'data' =>array(),
		);
		$this->response->addHeader('Content-Type: application/json');
		$encodedParam = $this->request->post['param'];
		// $encodedParam = "AwF6DWxpbUEEzamr9n8nJV01QTsso3810sMjbBz6VIyNQKAD2p5YscyNOXieEbSe4aKCBLX+3KY7WYrpM0fSJnVyws2Ue7pBgN6ajRNW8aaTUllVWmdQL/VYma+66HiGwh/1ayZfF1f6eHmBglzoMQprJg1fEjhtREwULh151ONpCg==";
		// $publickey = $this->config->get('rest_api_key');
		$headers = getallheaders();
		$deviceid = $headers["deviceid"];
		$device = $this->model_openapi_device->getDeviceByDeviceid($deviceid);
		$privatekey = $device["aeskey"];
		$cryptor = new \RNCryptor\Decryptor();
		$decodedParam = $cryptor->decrypt($encodedParam, $privatekey);
		$paramArray = $this->convertUrlQuery($decodedParam);
		if (count($paramArray) > 0) {
			$data = array(
				'customer_group_id' => 0,
				'fullname'			=> '',
				'firstname'         => '',
				'lastname'          => '',
				'email'             => isset($paramArray["email"])?$paramArray["email"]:'',
				'telephone'         => isset($paramArray["telephone"])?$paramArray["telephone"]:'',
				'fax'               => '',
				'password'          => isset($paramArray["password"])?$paramArray["password"]:'',
				'company'           => '',
				'address_1'         => '',
				'address_2'         => '',
				'city'              => '',
				'postcode'          => '',
				'country_id'        => 0,
				'zone_id'           => 0,
			);

			$this->load->model('account/customer');

			$customer_info = $this->model_account_customer->getCustomerByTelephone($data["telephone"]);
			if ($customer_info) {
				$json["code"] = 503;
				$json["message"] = $this->language->get('api_error_503');
				$this->response->setOutput(json_encode($json));
				return;
			}

			$customer_id = $this->model_account_customer->addCustomer($data);
			if ($customer_id) {
				$json["data"]["uid"] = $customer_id;
				$json["message"] = 'success';
			} else {
				$json["code"] = 500;
				$json["message"] = $this->language->get('api_error_add_customer_failed');
			}
		} else {
			$json["code"] = 500;
			$json["message"] = $this->language->get('api_error_invalid_param');
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function login() {
		require_once(DIR_SYSTEM . 'library/RNCryptor/autoload.php');

		$this->load->model('account/customer');
		$this->load->model('openapi/device');
		$this->load->model('tool/validate');

		$this->load->language('tool/validate');
		$this->load->language('account/login');
		$this->load->language('error/api_error');

		$json = array(
			'code'  => 200,
			'message'  => "",
			'data' =>array(),
		);

		$this->response->addHeader('Content-Type: application/json');

		$headers = getallheaders();
		$deviceid = $headers["deviceid"];
		$device = $this->model_openapi_device->getDeviceByDeviceid($deviceid);
		$privatekey = $device["aeskey"];

		if (isset($this->request->post['param'])) {
			$encodedParam = $this->request->post['param'];
		} else {
			$json["code"] = 500;
			$json["message"] = $this->language->get('api_error_invalid_param');
			$this->response->setOutput(json_encode($json));
			return;
		}
		// $encodedParam = "AwF6DWxpbUEEzamr9n8nJV01QTsso3810sMjbBz6VIyNQKAD2p5YscyNOXieEbSe4aKCBLX+3KY7WYrpM0fSJnVyws2Ue7pBgN6ajRNW8aaTUllVWmdQL/VYma+66HiGwh/1ayZfF1f6eHmBglzoMQprJg1fEjhtREwULh151ONpCg==";
		// $publickey = $this->config->get('rest_api_key');
		$cryptor = new \RNCryptor\Decryptor();

		$decodedParam = $cryptor->decrypt($encodedParam, $privatekey);
		$paramArray = $this->convertUrlQuery($decodedParam);
		if (count($paramArray) > 0) {
			// if (isset($paramArray["email"])) {
			// 	$email = $paramArray["email"];
			// }
			
			if (isset($paramArray["telephone"])) {
				$telephone = $paramArray["telephone"];
			}

			if (isset($paramArray["password"])) {
				$password = $paramArray["password"];
			}

			if(isset($telephone) && $this->model_tool_validate->validateTelephone($telephone)) {
				$customer_info = $this->model_account_customer->getCustomerByTelephone($telephone);

				if ($customer_info && !$customer_info['approved']) {
					$json["code"] = 504;
					$json["message"] = $this->language->get('api_error_504');
				} else if (!$this->customer->mobilelogin($telephone, $password)) {
					$json["code"] = 505;
					$json["message"] = $this->language->get('api_error_505');
					
					$this->model_account_customer->addLoginAttempt($telephone);
				} else {
					$customer_info = $this->model_account_customer->getCustomerByTelephone($telephone);
					$this->model_account_customer->deleteLoginAttempts($telephone);
				}
			} else {
				$json["code"] = 500;
				$json["message"] = $this->language->get('error_account');
			}
		} else {
			$json["code"] = 500;
			$json["message"] = $this->language->get('api_error_invalid_param');
		}

		if ($json["code"] == 200) {
			$json["data"]["uid"] = $customer_info["customer_id"];
			$json["data"]["fullname"] = $customer_info["fullname"];
			$json["data"]["email"] = $customer_info["email"];
			$json["data"]["telephone"] = $customer_info["telephone"];
			$json["data"]["token"] = $customer_info["api_token"];
		}
		
		$this->response->setOutput(json_encode($json));
	}

	public function logout() {
		$this->customer->mobilelogout();
	}

	public function orders() {
		$this->load->model('openapi/device');
		$this->load->model('openapi/authorize');
		$this->load->model('openapi/order');

		$this->load->language('error/api_error');
		$json = array(
			'code'  => 200,
			'message'  => "",
			'data' =>array(),
		);

		$headers = getallheaders();
    	$token = $headers["token"];

		if (isset($this->request->post['page'])) {
			$page = $this->request->post['page'];
		} else {
			$page = 1;
		}
		
		if (isset($this->request->post['limit'])) {
			$limit = $this->request->post['limit'];
		} else {
			$limit = $this->config->get('config_product_limit');
		}
		$start = ($page - 1) * $limit;
		$customer = $this->model_openapi_authorize->getCustomer($token);
		if (!$customer) {
			$json["code"] = 501;
			$json["message"] = $this->language->get('api_error_501');
		} else {
			$results = $this->model_openapi_order->getOrders($customer["customer_id"], $start, $limit);
			if (count($results) ) {
				foreach ($results as $result) {

					// $product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
					$products = $this->model_openapi_order->getOrderProducts($result['order_id']);
					foreach ($products as $product) {
						$order_products[] = array(
							'order_product_id' 	=> $product['order_product_id'],
							'order_id' 			=> $product['order_id'],
							'product_id' 		=> $product['product_id'],
							'product_name'		=> $product['name'],
							'image'				=> $product['image'],
							'quantity'			=> $product['op_quantity'],
						);
					}
					$json["data"]["rows"][] = array(
							'order_id'		=> $result['order_id'],
							'name'			=> $result['fullname'],
							'status_id'		=> $result['status_id'],
							'status_name'	=> $result['status'],
							'date_added'	=> $result['date_added'],
							'total'			=> $result['total'],
							'currency_code'	=> $result['currency_code'],
							'currency_value'=> $result['currency_value'],
							'products'		=> $order_products,
					);
				}
				$order_total = $this->model_openapi_order->getTotalOrders($customer["customer_id"]);
				$json["data"]["totalCount"] = $order_total;
				$json["data"]["pageSize"] = $limit;
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function printkey() {
		$json = array("success"=>false);

		/*check rest api is enabled*/
		if (!$this->config->get('rest_api_status')) {
			$json["error"] = 'API is disabled. Enable it!';
		}
		
		// /*validate api security key*/
		// if ($this->config->get('rest_api_key') && (!isset($this->request->get['key']) || $this->request->get['key'] != $this->config->get('rest_api_key'))) {
		// 	$json["error"] = 'Invalid secret key';
		// }
		$json["apikey"] = $this->config->get('rest_api_key');
		if(isset($json["error"])){
			$this->response->addHeader('Content-Type: application/json');
			echo(json_encode($json));
			exit;
		}else {
			$this->response->setOutput(json_encode($json));			
		}	
	}

	function convertUrlQuery($query) {
		$queryParts = explode('&', $query);
		$params = array();
		foreach ($queryParts as $param) {
			$item = explode('=', $param);
			$params[$item[0]] = $item[1];
		}
		return $params;
	}

	public function testToken() {
		$str = "mobile=18267163732&password=2jfsafdsafd";
		$arr_query = $this->convertUrlQuery($str);
		var_dump($arr_query);

		return;

		$json = array('success' => false);
		$this->response->addHeader('Content-Type: application/json');
		$this->load->model('openapi/device');
		$this->load->model('openapi/authorize');

		$customer = $this->model_api_authorize->getCustomer($this->request->get['deviceid'], $this->request->get['token']);
		if (!$customer) {
			$this->response->setOutput(json_encode($json));
			$json["error"] = 'invalid token';
			echo(json_encode($json));
			exit;
		} else {
			$json["success"] = true;
			$json["data"] = array('fullname' => $customer["fullname"]);
			$json["data"]["telephone"] = $customer["telephone"];
			$this->response->setOutput(json_encode($json));
		} 
	}
	
	public function testaes() {
		require_once(DIR_SYSTEM . 'library/RNCryptor/autoload.php');
		$password = "afff30da4232dea8dd7e787a3f62b029";
		$base64Encrypted = "AwFmkDGUCK1p4euA0Z1TQ3WrBLO03mnm9LOv9RvBxI32hPUhbFGkN3L86zz+mLr3HuRgWZo5EPmh0p4WoqHVGFDysyNPMWLu/aQqEj4UzkoqLuGTW+PNVGEptc1VZt9HvBtivtzM4s2kdBcMEm2K/szF";
		$cryptor = new \RNCryptor\Decryptor();

		$plaintext = $cryptor->decrypt($base64Encrypted, $password);
		echo "Base64 Encrypted:\n$base64Encrypted\n\n";
		echo "Plaintext:\n$plaintext\n\n";
	}
}
