<?php

namespace Banners\Widgets;

class Banner extends \Widget {

	// Component
	// ---------
	static $component = array(
		'id' => 'banner',
		'editable' => true,
		'title' => 'Вывод одного баннера',
	);

	// Get args format
	// ---------------
	public function getWidgetArgsFormat() {
		return array(
			'bannerID' => array('type' => 'object', 'class' => 'banner', 'title' => 'Баннер для вывода'),
			'group' => array('type' => 'text', 'title' => 'Группа баннеров'),
			'moreButton' => array('type' => 'dependent', 'title' => 'Выводить кнопку "подробнее"', 'format' => array(
				'moreButtonText' => array('type' => 'text', 'title' => 'Текст кнопки', 'value' => 'подробнее'),
			))
		);
	}


	// Render single banner
	// ---------------------
	public function renderBanner($banner) {

		// Nothing
		// -------
		if (empty($banner)) return;

		// Content is here
		// ---------------
		$content = '';

		// Если есть изображение
		// -----------
		if (!empty($banner->image)) {
			$mediaClass = \Core::getClass('media');
			$media = $mediaClass::findPK($banner->image);
			$content = '<div class="banner-image"><img src="'.$media->getURI().'"/></div>';
		}

		// Если есть текст
		// -----------
		if (!empty($banner->text)) {
			$content .= '<div class="banner-text">'.@$banner->text.'</div>';
		}

		// Add link
		// --------
		$bannerLink = '#';
		if (!empty($banner->link)) $bannerLink = $banner->link;

		// Если нужно выводить кнопку "подробнее"
		// -----------
		if (@$this->args['moreButton'] == true) {
			$content .= '<a href="'.$bannerLink.'" class="more-button">'.@first_var(@$this->args['moreButtonText'], 'подробнее').'</a>';
		} else {
			$content = '<a title="'.@ $banner->title.'" href="'.$bannerLink.'">'.$content.'</a>';

		}

		// Return content
		// --------------
		return $content;
	}

	// Render function
	// ---------------
	public function render() {

		$bannerClass = \Core::getClass('banner');

		// If group is given
		// -----------------
		if (!empty($this->args['group'])) {
			$banner = $bannerClass::find(array('query' => array('group' => $this->args['group'])));
			shuffle($banner);
			$banner = current($banner);
		}

		// Or banner
		// ---------
		elseif (!empty($this->args['bannerID'])) {
			$banner = $bannerClass::findOne(array('query' => array('_id' => $this->args['bannerID'])));
		}

		// If any banner is exists, return it
		// ----------------------------------
		$content = '';
		if (!empty($banner)) {
			$content = $this->renderBanner($banner);

			if (!empty($banner->htmlClass)) $this->options['htmlClasses'][] = $banner->htmlClass;
		}

		return $content;
	}

}