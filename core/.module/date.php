<?php

namespace Modules;

class Date {

	public static function to_case($timestr) {
	
		$conv = array(
			'Январь' => 'января',
			'Февраль' => 'февраля',
			'Март' => 'марта',
			'Апрель' => 'апреля',
			'Май' => 'мая',
			'Июнь' => 'июня',
			'Июль' => 'июля',
			'Август' => 'августа',
			'Сентябрь' => 'сентября',
			'Октябрь' => 'октября',
			'Ноябрь' => 'ноября',
			'Декабрь' => 'декабря'
		);
		$result = $timestr;
	
		foreach($conv as $index => $val) $result = str_replace($index, $val, $result);
		return $result;
	}


	// Ago
	// ---
	public static function ago($time) {		
		$d = time() - $time;
			if ($d < 60)
				return $d." секунд";
			else
			{
				$d = floor($d / 60);
				if($d < 60)
					return $d." минут";
				else
				{
					$d = floor($d / 60);
					if($d < 24)
						return $d." часов";
					else
					{
						$d = floor($d / 24);
						if($d < 7)
							return $d." дней";
						else
						{
							$d = floor($d / 7);
							if($d < 4)
								return $d." недель";
							else return time() - $time.' секунд';
						}//Week						
					}//Day
				}//Hour
			}//Minute

		return $d;
	}
}