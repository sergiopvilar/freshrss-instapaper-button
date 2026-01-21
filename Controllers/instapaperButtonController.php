<?php

class FreshExtension_instapaperButton_Controller extends Minz_ActionController
{
	public function jsVarsAction()
	{
		$extension = Minz_ExtensionManager::findExtension('InstapaperButton');
		if ($extension === null) {
			$extension = Minz_ExtensionManager::findExtension('Instapaper Button');
		}

		if ($extension === null) {
			http_response_code(500);
			echo '// Error: Extension not found';
			return;
		}

		$keyboard_shortcut = '';
		if (isset(FreshRSS_Context::$user_conf->instapaper_keyboard_shortcut)) {
			$keyboard_shortcut = FreshRSS_Context::$user_conf->instapaper_keyboard_shortcut;
		}

		$this->view->instapaper_button_vars = json_encode(array(
			'keyboard_shortcut' => $keyboard_shortcut,
			'icons' => array(
				'added_to_instapaper' => $extension->getFileUrl('added_to_instapaper.svg', 'svg'),
			),
			'i18n' => array(
				'added_article_to_instapaper' => _t('ext.instapaperButton.notifications.added_article_to_instapaper', '%s'),
				'failed_to_add_article_to_instapaper' => _t('ext.instapaperButton.notifications.failed_to_add_article_to_instapaper', '%s'),
				'ajax_request_failed' => _t('ext.instapaperButton.notifications.ajax_request_failed'),
				'article_not_found' => _t('ext.instapaperButton.notifications.article_not_found'),
			)
		));

		$this->view->_layout(false);
		$this->view->_path('instapaperButton/vars.js');

		header('Content-Type: application/javascript; charset=utf-8');
	}

	public function saveCredentialsAction()
	{
		$username = Minz_Request::param('username', '');
		$password = Minz_Request::param('password', '');
		$url_redirect = array('c' => 'extension', 'a' => 'configure', 'params' => array('e' => 'Instapaper Button'));

		if (empty($username) || empty($password)) {
			Minz_Request::bad(_t('ext.instapaperButton.notifications.credentials_required'), $url_redirect);
			return;
		}

		FreshRSS_Context::$user_conf->instapaper_username = $username;
		FreshRSS_Context::$user_conf->instapaper_password = $password;
		FreshRSS_Context::$user_conf->save();

		Minz_Request::good(_t('ext.instapaperButton.notifications.credentials_saved'), $url_redirect);
	}

	public function revokeAccessAction()
	{
		FreshRSS_Context::$user_conf->instapaper_username = '';
		FreshRSS_Context::$user_conf->instapaper_password = '';
		FreshRSS_Context::$user_conf->save();

		$url_redirect = array('c' => 'extension', 'a' => 'configure', 'params' => array('e' => 'Instapaper Button'));

		Minz_Request::good(_t('ext.instapaperButton.notifications.authorization_revoked'), $url_redirect);
	}

	public function addAction()
	{
		$this->view->_layout(false);

		$entry_id = Minz_Request::param('id');
		$entry_dao = FreshRSS_Factory::createEntryDao();
		$entry = $entry_dao->searchById($entry_id);

		if ($entry === null) {
			echo json_encode(array('status' => 404));
			return;
		}

		$post_data = array(
			'url' => $entry->link(),
			'title' => $entry->title()
		);

		$username = FreshRSS_Context::$user_conf->instapaper_username;
		$password = FreshRSS_Context::$user_conf->instapaper_password;

		$result = $this->curlPostRequest('https://www.instapaper.com/api/add', $post_data, $username, $password);
		$result['response'] = array('title' => $entry->title());

		echo json_encode($result);
	}

	private function curlPostRequest($url, $post_data, $username = '', $password = '')
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));

		if (!empty($username) && !empty($password)) {
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, $username . ':' . $password);
		}

		$response = curl_exec($curl);

		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$response_header = substr($response, 0, $header_size);
		$response_body = substr($response, $header_size);
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		$errorCode = 0;
		if ($http_code != 200 && $http_code != 201) {
			$errorCode = $http_code;
		}

		return array(
			'response' => $response_body,
			'status' => $http_code,
			'errorCode' => $errorCode
		);
	}

}
