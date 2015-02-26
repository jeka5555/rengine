<?php
/**
 * Возвращает окончание для множественного числа слова на основании числа и массива окончаний
 */
         
namespace Core\DataViews;

class NumEnding extends \Component {

	public static $component = array(
		'type' => 'dataView',
		'id' => 'num-ending'
	);

	// Отображение
	// -----------
	public function execute() {
	
		// Если входные данные невалидны, то возвращаем значение без изменений
		// -------------------
  	if (!is_numeric($this->value) or empty($this->options) or count($this->options) < 3) return $this->value; 	

	  $this->value = $this->value % 100;
	  if ($this->value>=11 && $this->value<=19) {
	      $ending=$this->options[2];
	  }
	  else {
	      $i = $this->value % 10;
	      switch ($i)
	      {
	          case (1): $ending = $this->options[0]; break;
	          case (2):
	          case (3):
	          case (4): $ending = $this->options[1]; break;
	          default: $ending=$this->options[2];
	      }
	  }
	  
	  return $ending;
	  
	}
}
