<?php


class ControllerOpenapiFeedback extends Controller {

	private $debugIt = false;
	
	public function addfeedback() {
		$this->load->model('openapi/feedback');
		$this->load->language('error/api_error');
		$this->load->model('openapi/authorize');

		$json = array(
			'code'  => 200,
			'message'  => "Success",
			'data' =>array(),
		);

		$data = array();

		$headers = getallheaders();
    	
    	if (isset($headers["token"])) {
    		$api_token = $headers["token"];
			$customer = $this->model_openapi_authorize->getCustomerByApiToken($api_token);
			if (isset($customer["customer_id"])) {
				$data["customer_id"] = $customer["customer_id"];
			}
    	}

		if (isset($this->request->post['content'])) {
			$data["content"] = $this->request->post['content'];
		}

		if (isset($this->request->post['email'])) {
			$data["email"] = $this->request->post['email'];
		}
		
		$this->model_openapi_feedback->addFeedback($data);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
