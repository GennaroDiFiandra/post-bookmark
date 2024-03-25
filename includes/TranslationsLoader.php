<?php

namespace PostBookmark;

defined('WPINC') || die;

class TranslationsLoader
{
  public function load_translations()
  {
    load_plugin_textdomain('post-bookmark', false, 'post-bookmark/languages');
  }

  public function setup_hooks()
  {
    return [
      ['after_setup_theme', 'load_translations', 10, 0],
    ];
  }
}