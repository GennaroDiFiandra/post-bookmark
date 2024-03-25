<?php

namespace PostBookmark;

defined('WPINC') || die;

class PluginStatus
{
  private static ?PluginStatus $instance = null;

  public static function instance():PluginStatus
  {
    if (self::$instance === null)self::$instance = new self();
    return self::$instance;
  }

  private function __construct() {}

  private function __clone() {}

  public function __wakeup() {}

  static public function on_deactivation()
  {
    flush_rewrite_rules();
  }
}