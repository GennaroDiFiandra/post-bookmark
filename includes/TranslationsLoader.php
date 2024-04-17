<?php

namespace PostBookmark;

defined('WPINC') || die;

class TranslationsLoader
{
  private const TEXTDOMAIN = 'post-bookmark';

  public function load_translations()
  {
    load_plugin_textdomain(self::TEXTDOMAIN, false, self::TEXTDOMAIN.'/languages');
  }

  public function setup_hooks()
  {
    return [
      ['after_setup_theme', 'load_translations', 10, 0],
    ];
  }
}