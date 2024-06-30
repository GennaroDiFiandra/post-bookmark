<?php

namespace PostBookmark\Templates;

defined('WPINC') || die;

class BookmarkMe
{
  public function print_button($content)
  {
    if (!is_user_logged_in()) return $content;
    if (!is_singular('post')) return $content;
    if ($this->is_post_already_bookmarked()) return $content;

    $user_id = get_current_user_id();
    $post_id = get_the_ID();
    $cta = __('Bookmark Me', 'post-bookmark');

    $content .= <<<CODE
      <button class="bookmark-me-button" data-postid="{$post_id}" data-userid="{$user_id}">
        $cta
      </button>
    CODE;

    return $content;
  }

  public function print_button_style()
  {
    $content = <<<CODE
      <style>
        .bookmark-me-button {
          border: none;
          padding: var(--bookmark-me-button-padding, 5px 10px);
          text-decoration: none;
          border-radius: var(--bookmark-me-button-radius, 50px);
          background-color: var(--bookmark-me-button-background, #000);
          color: var(--bookmark-me-button-color, #fff);
        }
      </style>
    CODE;

    echo $content;
  }

  /**
   * Determines whether the current post is already bookmarked.
   *
   * @return bool
   */
  private function is_post_already_bookmarked()
  {
    $user_id = get_current_user_id();
    $post_id = get_the_ID();
    $bookmarks = \get_user_meta($user_id, 'bookmarks', true);

    return (\is_array($bookmarks) && in_array($post_id, $bookmarks)) ? true : false;
  }
}