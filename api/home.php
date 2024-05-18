<?php

namespace nine\api;

class HomeController
{
    public function getMoreArticles($request)
    {
        $rt_config = rt_get_config();
        
        $query = $request->get_query_params();

        $posts_per_page = get_option('posts_per_page');
        $page = intval($query['page']);
        $post_type = isset($query['post_type']) ? $query['post_type'] : 'posts';
        
        $args = array(
            // 'post_type'=>'audios',
            'posts_per_page' => $posts_per_page,
            'paged' => $page,
            'post__not_in' => get_option( 'sticky_posts' ),
            // 'orderby' => array("menu_order" => "desc",'date' => "desc")
        );
        
        if( $rt_config['_home_options']['artilces_not_in_ids'] ){
            $args['category__not_in'] = explode(',', $rt_config['_home_options']['artilces_not_in_ids']);
        }
        
        $res = new \WP_Query( $args );

        $output = '';

        if ( $res->have_posts() ) {
            while ( $res->have_posts() ) {
                $res->the_post();
                $current_post = get_post();
                $thumbnail_arr = wp_get_attachment_image_src(get_post_thumbnail_id($current_post->ID), 'medium');
                $thumbnail = $thumbnail_arr ? $thumbnail_arr[0] : $rt_config['default_image'];
                
                $author_id = get_the_author_id();
                $avatar = rt_get_custom_avatar_url($author_id);
                
                $output .= '<div class="media-item">
                    <div class="media-thumbnail">
                        <a href="'.get_the_permalink() .'" target="_blank">
                            <img src="'.$thumbnail.'">
                        </a>
                    </div>
                    <div class="media-main">
                        <div class="media-title">
                            <a class="title" href="'.get_the_permalink() .'" target="_blank">'.get_the_title(). '</a>
                        </div>
                        <div class="media-description">
                            '. get_the_excerpt() .'
                        </div>
                        <div class="media-meta">
                            <a class="author" href="javascript:;">
                                <img src="'. $avatar .'">
                                <span>
                                    '.get_the_author_meta('display_name', $current_post->post_author) .'
                                </span>
                            </a>
                            <span class="date">
                                <i class="iconfont">&#xe76d;</i>'.get_the_time('Y-m-d').'
                            </span>
                            <span class="hit-counter">
                                <i class="iconfont">&#xe752;</i>' .getPostViews(get_the_ID()). '
                            </span>';
               
                if($rt_config['show_comments_counter']){
                    $output .= '<span class="meta"><i class="iconfont">&#xe8a6;</i>'. $current_post->comment_count .'</span>';
                }

                $output .= '
                        </div>
                    </div>
                </div>';
                
            }
            wp_reset_postdata();

            $response = new \WP_REST_Response(['html_str'=>$output]);
            $response->set_status(200);

            header("XXX-SQL-Query-Count: " . get_num_queries());
            return $response;
        } else {
            $response = new \WP_REST_Response([]);
            $response->set_status(404);
            return $response;
        }
    }
    
    
    function sendmail($request)
    {
        if (!is_user_logged_in() || !is_super_admin()) {
            wp_die("没有权限");
        }
    
        // 发送邮件
        $to         = $request->get_json_params()['email'];
        $subject    = "Nine 主题邮箱 SMTP 设置成功";
        $body       = "
        <div style='width:600px;margin:50px auto; padding-left:77px; background:#fff;font-size:16px;color:#55798d;padding-right80px;'>
            <p><img src='https://www.9-f.cn/wp-content/themes/nine/assets/images/logo.png' style='max-height:50px;user-select:none'></p>
            <h3>Nine 主题邮箱 SMTP 设置成功</h3>
            <p>Nine 主题官网地址 <a href='https://javascript.net.cn/projects/nine'>https://javascript.net.cn/projects/nine</a></p>
        </div>
        ";
        $headers    = array('Content-Type: text/html; charset=UTF-8');
    
        $flag = wp_mail($to, $subject, $body, $headers);
    
        if ($flag == true) {
            $response = new \WP_REST_Response(array("message" => "测试邮件发送成功"));
            $response->set_status(200);
            return $response;
        } else {
            $error_message = get_option('rt_mail_error');
            $response = new \WP_REST_Response(array(
                "message"   => "测试邮件发送失败",
                "detail"    => $error_message
            ));
            $response->set_status(500);
            return $response;
        }
    
        return $flag;
    }



}


