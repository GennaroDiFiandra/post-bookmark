<?php

namespace PostBookmark\Templates;

defined('WPINC') || die;

class BookmarkedPosts
{
  const ROUTE = 'bookmarked-posts';

  public function register_route()
  {
    $regex = '^'.self::ROUTE.'/?$';
    $query = 'index.php?'.self::ROUTE.'=true';
    $after = 'top';

    \add_rewrite_rule($regex, $query, $after);
    $this->update_rewrite_rules();
  }

  private function update_rewrite_rules()
  {
    if (!get_option('post_bookmark_are_rewrite_rules_flushed'))
    {
      flush_rewrite_rules();
      \update_option('post_bookmark_are_rewrite_rules_flushed', true, true);
    }
  }

  public function register_query_var($query_vars)
  {
    $query_vars[] = self::ROUTE;
    return $query_vars;
  }

  public function template($template)
  {
    if (!\get_query_var(self::ROUTE)) return $template;

    return POST_BOOKMARK_PATH.'views/bookmarked-posts.php';
  }

  public function print_button()
  {
    if (!is_user_logged_in()) return;
    if (\get_query_var(self::ROUTE)) return;

    $ref = \get_home_url().'/'.self::ROUTE.'/';
    $status = (\get_user_meta(get_current_user_id(), 'bookmarks', true)) ? '_active' : '';
    $cta = __('Bookmarked Posts', 'post-bookmark');

    $content = <<<CODE
      <a href="{$ref}" class="bookmarked-posts-button {$status}">
        $cta
      </a>
    CODE;

    echo $content;
  }

  public function print_button_style()
  {
    $content = <<<CODE
      <style>
        .bookmarked-posts-button {
          position: fixed;
          left: var(--bookmarked-posts-button-left, 5px);
          bottom: -100px;
          transition: translate .320s ease-in-out;
          padding: var(--bookmarked-posts-button-padding, 5px 10px);
          text-decoration: none;
          border-radius: var(--bookmarked-posts-button-radius, 50px);
          background-color: var(--bookmarked-posts-button-background, #000);
          color: var(--bookmarked-posts-button-color, #fff);
        }
        .bookmarked-posts-button._active {
          translate: 0 var(--bookmarked-posts-button-bottom, -105px);
        }
      </style>
    CODE;

    echo $content;
  }

  public function print_list_style()
  {
    $content = <<<CODE
      <style>
        .bookmarked-posts-item {
          list-style-type: '[X] ';
        }
        .bookmarked-posts-item:hover {
          cursor: pointer;
          color: #ff0000;
        }
      </style>
    CODE;

    echo $content;
  }

  public function setup_hooks()
  {
    return [
      ['init', 'register_route', 10, 1],
      ['query_vars', 'register_query_var', 10, 1],
      ['template_include', 'template', 10, 1],
      ['wp_footer', 'print_button', 10, 1],
      ['wp_head', 'print_button_style', 10, 1],
      ['wp_head', 'print_list_style', 10, 1],
    ];
  }
}