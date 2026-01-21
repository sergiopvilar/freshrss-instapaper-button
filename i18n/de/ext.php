<?php

return array(
	'instapaperButton' => array(
		'configure' => array(
			'username' => 'Benutzername',
			'password' => 'Passwort',
			'credentials_description' => '<ul class="ib_listedNumbers">
				<li>Gebe deinen Instapaper Benutzernamen und Passwort ein</li>
				<li>Du kannst deine normalen Instapaper Account-Daten verwenden</li>
				<li>Für mehr Sicherheit, erwäge die Verwendung eines app-spezifischen Passworts, falls verfügbar</li>
			</ul>
			<span>Weitere Details findest du in der <a href="https://www.instapaper.com/api" target="_blank">Instapaper API Dokumentation</a>!',
			'connect_to_instapaper' => 'Mit Instapaper verbinden',
			'keyboard_shortcut' => 'Tastaturkürzel',
			'extension_disabled' => 'Du musst die Erweiterung aktivieren, bevor du dich mit Instapaper verbinden kannst!',
			'connected_to_instapaper' => 'Du bist über den Account <b>%s</b> mit Instapaper verbunden!',
			'revoke_access' => 'Verbindung zu Instapaper trennen!',
		),
		'notifications' => array(
			'added_article_to_instapaper' => '<i>\'%s\'</i> erfolgreich zu Instapaper hinzugefügt!',
			'failed_to_add_article_to_instapaper' => 'Fehler beim hinzufügen des Artikels! HTTP Fehlercode: %s',
			'ajax_request_failed' => 'Ajax-Anfrage fehlgeschlagen!',
			'credentials_required' => 'Benutzername und Passwort sind erforderlich!',
			'credentials_saved' => 'Anmeldedaten erfolgreich gespeichert!',
			'article_not_found' => 'Artikel konnte nicht gefunden werden!',
			'authorization_revoked' => 'Autorisierung erfolgreich widerrufen!'
		)
	),
);
