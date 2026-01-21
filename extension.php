<?php

class InstapaperButtonExtension extends Minz_Extension {
	public function init() {
		$this->registerTranslates();

		Minz_View::appendScript($this->getFileUrl('script.js', 'js'), false, false, false);
		Minz_View::appendStyle($this->getFileUrl('style.css', 'css'));
		Minz_View::appendScript(_url('instapaperButton', 'jsVars'), false, true, false);

		$this->registerController('instapaperButton');
		$this->registerViews();
	}

	public function handleConfigureAction() {
		$this->registerTranslates();
		
		if (Minz_Request::isPost()) {
			$keyboard_shortcut = Minz_Request::param('keyboard_shortcut', '');
			FreshRSS_Context::$user_conf->instapaper_keyboard_shortcut = $keyboard_shortcut;
			FreshRSS_Context::$user_conf->save();
		}
	}

	public function isConfigured() {
		if (FreshRSS_Context::$user_conf->instapaper_username == '') {
			return false;
		}

		if (FreshRSS_Context::$user_conf->instapaper_password == '') {
			return false;
		}

		return true;
	}
}
