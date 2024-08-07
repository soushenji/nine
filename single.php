<?php

/**
 * Template Name: 普通文章
 * Template Post Type: post
 *
 * @author: soushenji <soushenji@qq.com>
 * @link https://github.com/soushenji
 */

get_header();
?>


<?php
$vt_config = vt_get_config();

setPostViews(get_the_ID()); // 更新文章浏览次数

$vt_post_id = get_the_ID();
$vt_post = get_post($vt_post_id, ARRAY_A);
$vt_author_id = $vt_post['post_author'];
$vt_avatar = vt_get_custom_avatar_url($vt_author_id);

global $wpdb;
$current_user_id = get_current_user_id();

// 喜欢数量
$like_counter = 0;
$sql = "SELECT count(*) AS num FROM {$wpdb->prefix}vt_star WHERE object_id=%s AND type=%s";
$res = $wpdb->get_row($wpdb->prepare($sql, [$vt_post_id, 'like']), ARRAY_A );
$like_counter = $res['num'];

// 当前用户是否喜欢
$is_like = false;
if($current_user_id){
    $sql = "SELECT id AS num FROM {$wpdb->prefix}vt_star WHERE object_id=%d AND user_id=%d AND type='like'";
    $res = $wpdb->get_row($wpdb->prepare($sql, [$vt_post_id, $current_user_id]), ARRAY_A );
    $is_like = $res ? true : false;
}

// 收藏数量
$star_counter = 0;
$sql = "SELECT count(*) AS num FROM {$wpdb->prefix}vt_star WHERE object_id=%s AND type=%s";
$res = $wpdb->get_row($wpdb->prepare($sql, [$vt_post_id, 'star']), ARRAY_A );
$star_counter = $res['num'];

// 当前用户是否收藏
$is_star = false;
if($current_user_id){
    $sql = "SELECT id AS num FROM {$wpdb->prefix}vt_star WHERE object_id=%d AND user_id=%d AND type='star'";
    $res = $wpdb->get_row($wpdb->prepare($sql, [$vt_post_id, $current_user_id]), ARRAY_A );
    $is_star = $res ? true : false;
}

?>


<div class="main-container">
    <div class="widget-one">
        <div class="singular-article-container">
            <div class="article-title">
                <?php the_title(); ?>
            </div>
            <div class="article-meta">
                <span class="meta author">
                    <img src="<?php echo $vt_avatar ?>" >
                    <span>
                        <a href="/users/<?php echo $post->post_author ?>" target='_blank'>
                            <?php echo get_the_author_meta('nickname', $post->post_author) ?>
                        </a>
                    </span>
                </span>
                <span class="meta date">
                    <i class="iconfont">&#xe76d;</i>
                    <?php the_time('Y-m-d'); ?>
                </span>
                <span class='meta hit-conuter'>
                    <i class="iconfont">&#xe752;</i>
                    <?php echo getPostViews(get_the_ID()); ?>
                </span>
                <?php if($vt_config['show_comments_counter']):?>
                    <span class='meta'>
                        <i class="iconfont">&#xe8a6;</i><?php echo $post->comment_count; ?>
                    </span>
                <?php endif ?>
                <?php if( current_user_can( 'manage_options' ) ): ?>
                     <span class="meta edit-button">
                         <a href="/wp-admin/post.php?post=<?php echo get_the_ID() ?>&action=edit">
                            <i class="iconfont">&#xe77a;</i> <span>编辑</span>
                        </a>
                    </span>
                <?php endif ?>
            </div>
            
            <div class="article-content">
                <?php /* if(get_the_excerpt()): ?>
                    <div class="content-excerpt">
                        <?php echo get_the_excerpt() ?>
                    </div>
                <?php endif */ ?>

                <?php
                if (have_posts()) {
                    while (have_posts()) {
                        the_post();
                        the_content();
                        // get_template_part( 'templates/content', get_post_type() );
                    }
                }
                ?>

                <?php if($vt_config['show_copyright']):?>
                    <div class="copyright-content">
                        <i class="iconfont">&#xe788;</i>
                        <?php echo $vt_config['show_copyright_text']; ?>
                    </div>
                <?php endif ?>

                <?php
                $tags = get_the_tags( $vt_post_id );
                ?>
                <?php if($tags):?>
                    <div class="article-tags-widget">
                        <?php foreach($tags as $k=>$v):?>
                            <a href="javascript:;" class="tag-item"><?php echo $v->name?></a>
                        <?php endforeach?>
                    </div>
                <?php endif ?>

            </div>
            <div class="content-action">
                <div class="widget-action like <?php echo $is_like ? ' active' : '' ?>">
                    <i class="iconfont">&#xe663;</i>
                    <span>点赞</span>
                    <span class='number'><?php echo $like_counter ?></span>
                </div>
                <div class="widget-action star <?php echo $is_star ? ' active' : '' ?>">
                    <i class="iconfont">&#xe882;</i>
                    <span>收藏</span>
                    <span class='number'><?php echo $star_counter ?></span>
                </div>
                <!-- div class="widget-action comment">
                    <i class="iconfont">&#xe8a6;</i>
                    <span>评论</span>
                </div -->
            </div>
        </div><!-- .singular-article-container -->

        <div class="article-prev-next-nav">
            <?php
            $prev_post = get_previous_post();
            $next_post = get_next_post();
            ?>
            <?php if ($prev_post) : ?>
                <div class="article-nav prev-nav">
                    <div class="article-link">
                        <a href="<?php echo get_permalink($prev_post->ID); ?>"><?php echo $prev_post->post_title; ?></a>
                    </div>
                    <div class="arrow-icon"><i class="iconfont">&#xe749;</i>上一篇</div>
                </div>
            <?php endif; ?>

            <?php if ($next_post) : ?>
                <div class="article-nav next-nav">
                    <div class="article-link">
                        <a href="<?php echo get_permalink($next_post->ID); ?>"><?php echo $next_post->post_title; ?></a>
                    </div>
                    <div class="arrow-icon">下一篇<i class="iconfont">&#xe748;</i></div>
                </div>
            <?php endif; ?>
        </div>

        <?php
        if ($vt_config['comments_is_on'] && (comments_open() || get_comments_number()) ) {
            comments_template();
        }
        ?>

    </div><!-- .widget-one -->

    <div class="sider little-widget">
        <?php if ( is_active_sidebar( 'posts-sidebar' ) ) : ?>
            <?php dynamic_sidebar( 'posts-sidebar' ); ?>
        <?php endif; ?>
    </div>
</div>


<input type="hidden" name="wp_create_nonce" value="<?php echo wp_create_nonce('wp_rest'); ?>">
<input type="hidden" name="post_id" value="<?php echo $vt_post_id ?>">


<?php if($vt_config['highlight_is_on']):?>
<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/assets/js/lib/highlight/styles/stackoverflow-light.min.css">
<script src="<?php bloginfo('template_url'); ?>/assets/js/lib/highlight/highlight.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        document.querySelectorAll('pre').forEach((el) => {
            hljs.highlightElement(el);
        });
    });
</script>
<?php endif ?>

<?php get_footer(); ?>
