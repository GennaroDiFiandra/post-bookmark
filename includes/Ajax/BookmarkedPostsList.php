<?php

namespace PostBookmark\Ajax;

defined('WPINC') || die;

class BookmarkedPostsList
{
  public function delete_post_id()
  {
    // check_ajax_referer(secondo parametro nella creazione del nonce, key del nonce nel payload js)
    \check_ajax_referer('bookmarked_posts_list_nonce', 'nonce', true);

    $user_id = \sanitize_text_field($_REQUEST['user_id']);
    $post_id = \sanitize_text_field($_REQUEST['post_id']);

    $bookmarks = \get_user_meta($user_id, 'bookmarks', true);
    if (!\is_array($bookmarks)) $bookmarks = [];

    if (\in_array($post_id, $bookmarks))
    {
      $post_id_key = \array_search($post_id, $bookmarks);
      unset($bookmarks[$post_id_key]);
      \update_user_meta($user_id, 'bookmarks', $bookmarks);
      wp_send_json_success();
    }

    wp_send_json_error();
  }
}