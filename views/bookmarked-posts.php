<?php defined('WPINC') || die; ?>

<?php
  use PostBookmark\DataGenerator\BookmarkedPosts;
  $user_id = get_current_user_id();
  $bookmarked_posts = new BookmarkedPosts($user_id);
  $v=get_user_meta(get_current_user_id(), 'bookmarks', true);
  var_dump($v);
?>

<?php get_header(); ?>

<main class="container-xxl g-0">

  <section class="row">

    <?php if (!$bookmarked_posts->is_user_logged_in()) : ?>
      <p><?php echo __('This feature is available only for logged in users.', 'post-bookmark') ?></p>
    <?php elseif (!$bookmarked_posts->posts_ids()) : ?>
      <p><?php echo __('You have not saved posts yet.', 'post-bookmark') ?></p>
    <?php else : ?>

      <h1><?php echo __('Your Bookmarked Posts', 'post-bookmark') ?></h1>
      <?php
        $output = '<ul class="bookmarked-posts-list">';
        foreach ($bookmarked_posts->posts() as $post)
        {
          $post_id = $post->ID;
          $title = $post->post_title;
          $ref = get_permalink($post->ID);

          $output .= <<<CODE
            <li class="bookmarked-posts-item">
              <a href="{$ref}" class="bookmarked-posts-item-ref" data-postid="{$post_id}" data-userid="{$user_id}">{$title}</a>
            </li>
          CODE;
        }
        $output .= '</ul>';

        echo $output;
      ?>

    <?php endif; ?>

  </section>

</main>

<?php get_footer(); ?>