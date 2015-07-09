<?php


class ControllerOpenapiProducts extends Controller {

	private $debugIt = false;
	
	public function getproducts() {
		$this->load->language('product/search');

		$this->load->model('catalog/category');

		$this->load->model('openapi/product');

		$this->load->model('tool/image');

		$this->load->language('error/api_error');
		$json = array(
			'code'  => 200,
			'message'  => "",
			'data' =>array(),
		);

		$this->response->addHeader('Content-Type: application/json');

		$search = isset($this->request->post['keyword'])?$this->request->post['keyword']:'';

		if (isset($this->request->post['tag'])) {
			$tag = $this->request->post['tag'];
		} elseif (isset($this->request->post['keyword'])) {
			$tag = $this->request->post['keyword'];
		} else {
			$tag = '';
		}

		if (isset($this->request->post['description'])) {
			$description = $this->request->post['description'];
		} else {
			$description = '';
		}

		if (isset($this->request->post['category_id'])) {
			$category_id = $this->request->post['category_id'];
		} else {
			$category_id = 0;
		}
		
		if (isset($this->request->post['sub_category'])) {
			$sub_category = $this->request->post['sub_category'];
		} else {
			$sub_category = '';
		}

		if (isset($this->request->post['sort'])) {
			$sort_int = $this->request->post['sort'];
			if ($sort_int == 0) {
				$sort = 'p.sort_order';
			} else if ($sort_int == 1) {
				$sort = 'p.price';
			} else if ($sort_int == 2) {
				$sort = 'rating';
			} else if ($sort_int == 3) {
				$sort = 'p.quantity';
			}
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->post['order'])) {
			$order_int = $this->request->post['order'];
			if ($order_int == 0) {
				$order = 'ASC';
			} else {
				$order = 'DESC';
			}
		} else {
			$order = 'ASC';
		}

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

		if (isset($this->request->post['price_low'])) {
			$price_low = $this->request->post['price_low'];
		} 

		if (isset($this->request->post['price_high'])) {
			$price_high = $this->request->post['price_high'];
		} 

		if (isset($this->request->post['keyword']) || isset($this->request->post['tag'])) {
			$filter_data = array(
				'filter_name'         => $search,
				'filter_tag'          => $tag,
				'filter_description'  => $description,
				'filter_category_id'  => $category_id,
				'filter_sub_category' => $sub_category,
				'sort'                => $sort,
				'order'               => $order,
				'start'               => ($page - 1) * $limit,
				'limit'               => $limit
			);

			if (isset($price_low) && isset($price_high) && $price_low < $price_high) {
				$filter_data["pricerange_low"] = $price_low;
				$filter_data["pricerange_high"] = $price_high;
			}

			$product_total = $this->model_openapi_product->getTotalProducts($filter_data);
			$json["data"]["totalCount"] = $product_total;
			$json["data"]["pageSize"] = $limit;
			$results = $this->model_openapi_product->getProducts($filter_data);

			foreach ($results as $result) {
				if ($result['image']) {
					// $image = $result['image'];
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'));
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax'));
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$json['data']['rows'][] = array(
					'productId'  => $result['product_id'],
					'thumbUrl'       => $image,
					'productName'        => $result['name'],
					'productdesc' => $result['description'],
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating']
					// 'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url)
				);
			}

		} else {
			$json['code'] = 500;
			$json['message'] = $this->language->get('api_error_invalid_param');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	public function categories() {

		$this->load->model('catalog/category');
		$json = array(
			'code'  => 200,
			'message'  => "",
			'data' =>array(),
		);

		# -- $_GET params ------------------------------
		
		if (isset($this->request->post['parent'])) {
			$parent = $this->request->post['parent'];
		} else {
			$parent = 0;
		}

		if (isset($this->request->post['level'])) {
			$level = $this->request->post['level'];
		} else {
			$level = 1;
		}

		# -- End $_GET params --------------------------

		$json['data']['rows'] = $this->getCategoriesTree($parent, $level);

		// if ($this->debug) {
		// 	echo '<pre>';
		// 	print_r($json);
		// } 

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Generation of category tree
	 * 
	 * @param  int    $parent  Prarent category id
	 * @param  int    $level   Depth level
	 * @return array           Tree
	 */
	private function getCategoriesTree($parent = 0, $level = 1) {
		$this->load->model('catalog/category');
		$this->load->model('tool/image');
		
		$result = array();

		$categories = $this->model_catalog_category->getCategories($parent);

		if ($categories && $level > 0) {
			$level--;

			foreach ($categories as $category) {

				if ($category['image']) {
					// $image = $category['image'];
					$image = $this->model_tool_image->resize($category['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));
				}

				$result[] = array(
					'categoryId'   	=> $category['category_id'],
					'parentId'     	=> $category['parent_id'],
					'categoryName' 	=> $category['name'],
					'image'         => $image,
					'description'	=> $category['description'],
					'href'          => $this->url->link('product/category', 'category_id=' . $category['category_id']),
					'categories'    => $this->getCategoriesTree($category['category_id'], $level)
				);
			}

			return $result;
		}
	}
}
