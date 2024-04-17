<?php

namespace PostBookmark\Ajax;

defined('WPINC') || die;

class BookmarkMeButton
{
  public function save_post_id()
  {
    // check_ajax_referer(secondo parametro nella creazione del nonce, key del nonce nel payload js)
    \check_ajax_referer('bookmark_me_button_nonce', 'nonce', true);

    $user_id = \sanitize_text_field($_REQUEST['user_id']);
    $post_id = \sanitize_text_field($_REQUEST['post_id']);

    $bookmarks = \get_user_meta($user_id, 'bookmarks', true);
    if (!\is_array($bookmarks)) $bookmarks = [];

    if (!\in_array($post_id, $bookmarks))
    {
      $bookmarks[] = $post_id;
      \update_user_meta($user_id, 'bookmarks', $bookmarks);
      wp_send_json_success();
    }

    wp_send_json_error();
  }

  public function setup_hooks()
  {
    return [
      ['wp_ajax_bookmark_me_button', 'save_post_id', 10, 1],
    ];
  }
}