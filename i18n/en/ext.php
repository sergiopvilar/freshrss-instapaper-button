<?php

return array(
	'instapaperButton' => array(
		'configure' => array(
			'username' => 'Username',
			'password' => 'Password',
			'credentials_description' => '<ul class="ib_listedNumbers">
				<li>Enter your Instapaper username and password</li>
				<li>You can use your regular Instapaper account credentials</li>
				<li>For better security, consider using an app-specific password if available</li>
			</ul>
			<span>Details can be found on <a href="https://www.instapaper.com/api" target="_blank">Instapaper API documentation</a>!',
			'connect_to_instapaper' => 'Connect to Instapaper',
			'keyboard_shortcut' => 'Keyboard shortcut',
			'extension_disabled' => 'You need to enable the extension before you can connect to Instapaper!',
			'connected_to_instapaper' => 'You are connected to Instapaper with the account <b>%s</b>.',
			'revoke_access' => 'Disconnect from Instapaper!'
		),
		'notifications' => array(
			'added_article_to_instapaper' => 'Successfully added <i>\'%s\'</i> to Instapaper!',
			'failed_to_add_article_to_instapaper' => 'Adding article to Instapaper failed! HTTP error code: %s',
			'ajax_request_failed' => 'Ajax request failed!',
			'credentials_required' => 'Username and password are required!',
			'credentials_saved' => 'Credentials saved successfully!',
			'article_not_found' => 'Can\'t find article!',
			'authorization_revoked' => 'Authorization successfully revoked!'
		)
	),
);
