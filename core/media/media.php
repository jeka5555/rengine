<?php

namespace Core\Modules;

class Media extends \Core\Module
{

	public $mediaDirectory = 'private/media';
	
	// Module install
	// --------------
	public function installModule() {
		mkdir(__DR__ . $this->mediaDirectory, 0777);
	}

}