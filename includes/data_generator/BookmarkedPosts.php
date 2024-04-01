<?php

namespace PostBookmark\Data_Generator;

defined('WPINC') || die;

class BookmarkedPosts
{
  private int $user_id;

  public function __construct($user_id)
  {
    $this->user_id = $user_id;
  }

  public function is_user_logged_in()
  {
    return $this->user_id ? true : false;
  }

  public function posts_ids()
  {
    return ($this->user_id) ? \get_user_meta($this->user_id, 'bookmarks', true) : null;
  }

  public function posts()
  {
    return ($this->posts_ids()) ? get_posts([ 'include' => $this->posts_ids(), 'post_type' => 'event' ]) : null;
  }
}