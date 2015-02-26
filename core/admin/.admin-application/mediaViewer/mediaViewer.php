<?php

namespace Core\Admin\AdminApplications;

class MediaViewer extends \Core\Admin\Components\AdminApplication {

	// Component
	// ---------
	public static $component = array(
		'type' => 'admin-application',
		'id' => 'mediaViewer',
		'title' => 'Просмотр файлов'
	);

	// Init application
	// ----------------
	function commandInit($args = array()) {

		// Grards
		// ------
		if (empty($args['mediaID'])) return null;

		// Get preview
		// -----------
		$content = \Widgets::get('media', array('mediaID' => $args['mediaID'], 'width' => 400, 'height' => 400));

		// Get media
		// ---------
		$mediaClass = \Core::getClass('media');
		$media = $mediaClass::findPK($args['mediaID']);
		if (empty($media)) return;


		// Media title
		// -----------
		$info = '<div class="info-item"><strong>Название: </strong>'. $media->title.'</div>';
		$info .= '<div class="info-item"><strong>Время создания: </strong>'. \DataView::get('datetime', $media->get('@createTime')).'</div>';

		// Media type
		// ----------
		switch ($media->type) {
			case "image": $type = 'изображение'; break;
			case "audio": $type = 'аудио'; break;
			case "video": $type = 'видео'; break;
			case "youtube": $type = 'YouTube'; break;
			case "vimeo": $type = 'Vimeo'; break;
			default: $type = 'документ'; break;
		}
		$info .= '<div class="info-item"><strong>Тип файла: </strong>'. $type.'</div>';

		// Media size
		// ----------
		if (!empty($media->fileSize)) {
			$info .= '<div class="info-item"><strong>Объем файла: </strong>'.$media->fileSize.' байт</div>';
		}

		// Get owner info
		// --------------
		if (@ $owner = $media->get('@owner')) {
			$userClass = \Core::getClass('user');
			$user = $userClass::findPK($owner);
			if (!empty($user)) {
				$info .= '<div class="info-item"><strong>Владелец: </strong>'.$user->fullName.'</div>';
			}
		}

		// Возврат
		// -------
		return array(
			'content' => $content,
			'info' => @ $info
		);

	}

}
