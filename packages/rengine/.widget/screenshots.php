<?php
namespace REngine\Widgets;

class Screenshots extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'id' => 'screenshots',
		'title' => 'REngine.Скриншоты'
	);

	
  // Render
  // ------
  public function render() {

    $content = '
        <h2>Скриншоты</h2>
        <div class="screenshots-list">
            <a href="/'.static::$component['packagePath'].'/.assets/img/screenshots/1_m.jpg"><img src="/'.static::$component['packagePath'].'/.assets/img/screenshots/1.jpg"></a>
            <a href="/'.static::$component['packagePath'].'/.assets/img/screenshots/2_m.jpg"><img src="/'.static::$component['packagePath'].'/.assets/img/screenshots/2.jpg"></a>
            <a href="/'.static::$component['packagePath'].'/.assets/img/screenshots/3_m.jpg"><img src="/'.static::$component['packagePath'].'/.assets/img/screenshots/3.jpg"></a>
            <a href="/'.static::$component['packagePath'].'/.assets/img/screenshots/4_m.jpg"><img src="/'.static::$component['packagePath'].'/.assets/img/screenshots/4.jpg"></a>
        </div>
    ';

    return $content;

  }
}
