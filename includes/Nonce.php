<?php

namespace PostBookmark;

defined('WPINC') || die;

class Nonce
{
  private string $hook_name;
  private string $nonce_name;

  public function __construct($hook_name, $nonce_name)
  {
    $this->hook_name = $hook_name;
    $this->nonce_name = $nonce_name;
  }

  public function add_nonce_to_array($data)
  {
    $data['nonce'] = wp_create_nonce($this->nonce_name);
    return $data;
  }
}