<?php

namespace PostBookmark;

defined('WPINC') || die;

class TranslationsLoader
{
  private const TEXT_DOMAIN = 'post-bookmark';

  public function load_translations()
  {
    load_plugin_textdomain(self::TEXT_DOMAIN, false, self::TEXT_DOMAIN.'/languages');
  }
}