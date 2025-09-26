<?php declare(strict_types=1); defined('WPINC') || die;

/*
  Plugin Name: Post Bookmark
  Plugin URI: #
  Author: Gennaro Di Fiandra
  Author URI: #
  Description: Allows the logged in users to save each post in a personal list.
  Version: 1.0.0
  Text Domain: post-bookmark
  Domain Path: /languages
  Requires at least: 6.0
  Requires PHP: 7.4
  License: GPLv2 or later
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

define('POST_BOOKMARK_VERSION', '1.0.0');
define('POST_BOOKMARK_PATH', plugin_dir_path(__FILE__));
define('POST_BOOKMARK_URL', plugin_dir_url(__FILE__));

use PostBookmark\Ajax\BookmarkMeButton as BookmarkMeButtonRequest;
use PostBookmark\Ajax\BookmarkedPostsList as BookmarkedPostsListRequest;
use PostBookmark\Templates\BookmarkMe;
use PostBookmark\Templates\BookmarkedPosts;
use PostBookmark\Script;
use PostBookmark\InlineScript;
use PostBookmark\Nonce;
use PostBookmark\TranslationsLoader;
use PostBookmark\PluginStatus;
use PostBookmark\ServiceContainer;

final class PostBookmark
{
  private static ?PostBookmark $instance = null;
  private ServiceContainer $container;

  public static function instance(): PostBookmark
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function __construct()
  {
    $this->container = new ServiceContainer();
  }

  private function __clone() {}

  public function __wakeup() {}

  private function require_resources()
  {
    // autoload all classes from includes directory
    require_once __DIR__ . '/vendor/autoload.php';
  }

  private function register_services()
  {
    $this->container->bind('bookmark_me', fn() => new BookmarkMe());
    $this->container->bind('bookmark_me_request', fn() => new BookmarkMeButtonRequest());
    $this->container->bind('bookmark_me_inline_script', fn() => new InlineScript(
      'post-bookmark-generated-scripts',
      'BookmarkMeButtonData',
      ['ajaxUrl' => admin_url('admin-ajax.php')],
      'before'
    ));
    $this->container->bind('bookmark_me_nonce', fn() => new Nonce('BookmarkMeButtonData', 'bookmark_me_button_nonce'));

    $this->container->bind('bookmarked_posts', fn() => new BookmarkedPosts());
    $this->container->bind('bookmarked_posts_list_request', fn() => new BookmarkedPostsListRequest());
    $this->container->bind('bookmarked_posts_list_inline_script', fn() => new InlineScript(
      'post-bookmark-generated-scripts',
      'BookmarkedPostsListData',
      ['ajaxUrl' => admin_url('admin-ajax.php')],
      'before'
    ));
    $this->container->bind('bookmarked_posts_list_nonce', fn() => new Nonce('BookmarkedPostsListData', 'bookmarked_posts_list_nonce'));

    $this->container->bind('generated_scripts', fn() => new Script('post-bookmark-generated-scripts', POST_BOOKMARK_URL . '/assets/_dist_/generated-scripts.js'));
    $this->container->bind('translations_loader', fn() => new TranslationsLoader());
  }

  public function init()
  {
    $this->require_resources();
    $this->register_services();

    // Handle "Bookmark Me" button and logic
    $bookmark_me = $this->container->get('bookmark_me');
    add_filter('the_content', [$bookmark_me, 'print_button'], 10, 1);
    add_action('wp_head', [$bookmark_me, 'print_button_style'], 10, 0);

    // Setups ajax request for "Bookmark Me" button
    $bookmark_me_request = $this->container->get('bookmark_me_request');
    add_action('wp_ajax_bookmark_me_button', [$bookmark_me_request, 'save_post_id'], 10, 0);

    // Setup dependencies for "Bookmark Me" button (InlineScript and Nonce)
    $bookmark_me_inline_script = $this->container->get('bookmark_me_inline_script');
    add_action('wp_enqueue_scripts', [$bookmark_me_inline_script, 'register_script'], 11, 0);
    $bookmark_me_nonce = $this->container->get('bookmark_me_nonce');
    add_filter('BookmarkMeButtonData', [$bookmark_me_nonce, 'add_nonce_to_array'], 10, 1);

    // Handle "Bookmarked Posts" button, list, view and logic
    $bookmarked_posts = $this->container->get('bookmarked_posts');
    add_action('init', [$bookmarked_posts, 'register_route'], 10, 0);
    add_filter('query_vars', [$bookmarked_posts, 'register_query_var'], 10, 1);
    add_filter('template_include', [$bookmarked_posts, 'template'], 10, 1);
    add_action('wp_footer', [$bookmarked_posts, 'print_button'], 10, 0);
    add_action('wp_head', [$bookmarked_posts, 'print_button_style'], 10, 0);
    add_action('wp_head', [$bookmarked_posts, 'print_list_style'], 10, 0);

    // Setups ajax request for "Bookmarked Posts" list
    $bookmarked_posts_list_request = $this->container->get('bookmarked_posts_list_request');
    add_action('wp_ajax_bookmarked_posts_list', [$bookmarked_posts_list_request, 'delete_post_id'], 10, 0);

    // Setup dependencies for "Bookmarked Posts" list (InlineScript and Nonce)
    $bookmarked_posts_list_inline_script = $this->container->get('bookmarked_posts_list_inline_script');
    add_action('wp_enqueue_scripts', [$bookmarked_posts_list_inline_script, 'register_script'], 11, 0);
    $bookmarked_posts_list_nonce = $this->container->get('bookmarked_posts_list_nonce');
    add_filter('BookmarkedPostsListData', [$bookmarked_posts_list_nonce, 'add_nonce_to_array'], 10, 1);

    // Add compiled js files
    $generated_scripts = $this->container->get('generated_scripts');
    add_action('wp_enqueue_scripts', [$generated_scripts, 'register_script'], 10, 0);

    // Load strings translations
    $translations_loader = $this->container->get('translations_loader');
    add_action('after_setup_theme', [$translations_loader, 'load_translations'], 10, 0);
  }
}
PostBookmark::instance()->init();
register_deactivation_hook(__FILE__, [PluginStatus::instance(), 'on_deactivation']);