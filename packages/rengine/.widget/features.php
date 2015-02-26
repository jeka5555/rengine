<?php
namespace REngine\Widgets;

class Features extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'id' => 'features',
		'title' => 'REngine.Особенности'
	);

	
  // Render
  // ------
  public function render() {

    $content = '
    <div class="widget-wrapInner width-wrap">
        <h2>Особенности и возможности</h2>
        <div class="features-list">
            <div class="featur-item">
                <div class="img-wrap"><img src="/'.static::$component['packagePath'].'/.assets/img/features/1.jpg"></div>
                <div class="title">Скорость разработки</div>
            </div>
            <div class="featur-item">
                <div class="img-wrap"><img src="/'.static::$component['packagePath'].'/.assets/img/features/2.jpg"></div>
                <div class="title">Удобство использования</div>
            </div>
            <div class="featur-item">
                <div class="img-wrap"><img src="/'.static::$component['packagePath'].'/.assets/img/features/3.jpg"></div>
                <div class="title">Оптимизация под SEO</div>
            </div>
            <div class="featur-item">
                <div class="img-wrap"><img src="/'.static::$component['packagePath'].'/.assets/img/features/4.jpg"></div>
                <div class="title">Поддержка</div>
            </div>
            <div class="featur-item">
                <div class="img-wrap"><img src="/'.static::$component['packagePath'].'/.assets/img/features/5.jpg"></div>
                <div class="title">Страницы без перезагрузки</div>
            </div>
            <div class="featur-item">
                <div class="img-wrap"><img src="/'.static::$component['packagePath'].'/.assets/img/features/6.jpg"></div>
                <div class="title">Скорость работы</div>
            </div>
        </div>
    </div>
    ';

    return $content;

  }
}
