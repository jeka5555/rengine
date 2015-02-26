<?php

// Загрузчик Twig
// --------------
class TwigLoader implements \Twig_LoaderInterface {
  public function getSource($name) { return $name; }
  public function getCacheKey($name) { return md5($name); }
  public function isFresh($name, $time) { return true; }
}
