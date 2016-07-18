<?php

class ControllerFeedRestApi extends Controller {

	public function index() {
		$this->load->language('feed/rest_api');
		$this->load->model('setting/setting');

		$this->document->setTitle($this->language->get('heading_title'));

		$data = array(
			'version'             => '0.1',
			'heading_title'       => $this->language->get('heading_title'),
			
			'text_enabled'        => $this->language->get('text_enabled'),
			'text_disabled'       => $this->language->get('text_disabled'),
			'tab_general'         => $this->language->get('tab_general'),

			'entry_status'        => $this->language->get('entry_status'),
			'entry_key'           => $this->language->get('entry_key'),

			'button_save'         => $this->language->get('button_save'),
			'button_cancel'       => $this->language->get('button_cancel'),
			'text_edit'           => $this->language->get('text_edit'),

			'action'              => $this->url->link('feed/rest_api', 'token=' . $this->session->data['token'], 'SSL'),
			'cancel'              => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL')
		);

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_setting_setting->editSetting('rest_api', $this->request->post);				
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'));
		}

  		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_feed'),
			'href'      => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'),       		
			'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('feed/rest_api', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
   		);

   		if (isset($this->request->post['rest_api_status'])) {
			$data['rest_api_status'] = $this->request->post['rest_api_status'];
		} else {
			$data['rest_api_status'] = $this->config->get('rest_api_status');
		}

		if (isset($this->request->post['rest_api_key'])) {
			$data['rest_api_key'] = $this->request->post['rest_api_key'];
		} else {
			$data['rest_api_key'] = $this->config->get('rest_api_key');
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('feed/rest_api.tpl', $data));
	}

	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "device` (
  			`deviceid` varchar(32) NOT NULL,
  			`uuid` varchar(32) NOT NULL,
  			`aeskey` varchar(32) NOT NULL,
  			`appversion` varchar(32) NOT NULL,
  			`sysname` varchar(32) NOT NULL,
  			`sysversion` varchar(32) NOT NULL,
  			PRIMARY KEY (`deviceid`)
  		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "feedback` (
  			`question_id` varchar(32) NOT NULL,
  			`customer_id` varchar(32) NOT NULL,
  			`content` varchar(32) NOT NULL,
  			`email` varchar(32) NOT NULL,
  			PRIMARY KEY (`question_id`)
  		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD COLUMN api_token VARCHAR(32) DEFAULT NULL;");
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "device`;");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "feedback`;");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` DROP COLUMN api_token;");
	}

}
