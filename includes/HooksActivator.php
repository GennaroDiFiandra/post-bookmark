<?php

namespace PostBookmark;

defined('WPINC') || die;

class HooksActivator
{
  public function activate_hooks($object)
  {
    $hooks_book = $object->setup_hooks();

    foreach ($hooks_book as $hooks_book_item)
    {
      $hook = $hooks_book_item[0];
      $callback = $hooks_book_item[1];
      $priority = $hooks_book_item[2];
      $accepted_args = $hooks_book_item[3];

      add_filter($hook, [$object,$callback], $priority, $accepted_args);
    }
  }
}