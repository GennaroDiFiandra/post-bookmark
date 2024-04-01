<?php

namespace PostBookmark;

defined('WPINC') || die;

class Script
{
  private string $unique_identifier;
  private string $src;
  private array $deps;
  private string $version;
  private bool $in_footer;

  public function __construct($unique_identifier, $src, $deps=[], $in_footer=true)
  {
    $this->unique_identifier = $unique_identifier;
    $this->src = $src;
    $this->deps = $deps;
    $this->version = wp_get_theme()->get('Version');
    $this->in_footer = $in_footer;
  }

  public function register_script()
  {
    wp_enqueue_script($this->unique_identifier, $this->src, $this->deps, $this->version, $this->in_footer);
  }

  public function setup_hooks()
  {
    return [
      ['wp_enqueue_scripts', 'register_script', 10, 0],
    ];
  }
}