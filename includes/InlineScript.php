<?php

namespace PostBookmark;

defined('WPINC') || die;

class InlineScript
{
  private string $script_identifier;
  private string $data_key;
  private array $data_value;
  private string $position;

  public function __construct($script_identifier, $data_key, $data_value, $position)
  {
    $this->script_identifier = $script_identifier;
    $this->data_key = $data_key;
    $this->data_value = $data_value;
    $this->position = $position;
  }

  private function get_data()
  {
    return $this->set_data();
  }

  private function set_data()
  {
    $this->data_value =  apply_filters($this->data_key, $this->data_value);
    $encoded_data_value = $this->encode_data_value($this->data_value);
    return "const {$this->data_key} = {$encoded_data_value}";
  }

  private function encode_data_value()
  {
    return json_encode($this->data_value);
  }

  public function register_script()
  {
    wp_add_inline_script($this->script_identifier, $this->get_data(), $this->position);
  }

  public function setup_hooks()
  {
    return [
      ['wp_enqueue_scripts', 'register_script', 10, 0],
    ];
  }
}