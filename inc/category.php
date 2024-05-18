<?php

/**
 * 后台编辑分类页，添加自定义字段
 */


/**
 * 分类添加时的字段
 */
function rt_add_category_field()
{
    echo '<div class="form-field">
        <label for="cat-keywords">关键词</label>
        <input name="cat-keywords" id="cat-keywords" type="text" value="" size="40">
        <p>输入关键词</p>
        </div>';
    echo '<div class="form-field">
          <label for="cat-thumbnail">缩略图</label>
          <input name="cat-thumbnail" id="cat-thumbnail" type="text" value="" size="40">
          <p>输入缩略图的地址</p>
        </div>';
}
add_action('category_add_form_fields', 'rt_add_category_field', 10, 2);


/**
 * 编辑分类时的字段
 */
function rt_edit_category_field($tag)
{
    echo '<tr class="form-field">
            <th scope="row"><label for="cat-keywords">关键词</label></th>
            <td>
                <input name="cat-keywords" id="cat-keywords" type="text" value="' . get_option('cat-keywords-' . $tag->term_id) . '" size="40"/><br>
                <p class="description">' . $tag->name . ' 关键词</p>
            </td>
        </tr>';
    echo '<tr class="form-field">
        <th scope="row"><label for="cat-thumbnail">缩略图</label></th>
        <td>
        <input name="cat-thumbnail" id="cat-thumbnail" type="text" value="' . get_option('cat-thumbnail-' . $tag->term_id) . '" size="40"/><br>
        <p class="description">输入缩略图的地址</p>
        </td>
        </tr>';
}
add_action('category_edit_form_fields', 'rt_edit_category_field', 10, 2);


/**
 * 保存数据
 */
function rt_taxonomy_metadate($term_id)
{
    if (!current_user_can('manage_categories')) {
        return $term_id;
    }
    
    if (isset($_POST['cat-keywords'])) {
        $cat_key = 'cat-keywords-' . $term_id;
        $cat_value = $_POST['cat-keywords'];
        update_option($cat_key, $cat_value);
    }
    
    if (isset($_POST['cat-thumbnail'])) {
        $cat_key = 'cat-thumbnail-' . $term_id;
        $cat_value = $_POST['cat-thumbnail'];
        update_option($cat_key, $cat_value);
    }
    
    if (isset($_POST['rt_cat_tpl'])) {
        $key   = 'rt_cat_tpl_' . $term_id;
        $value = $_POST['rt_cat_tpl'];
        update_option($key, $value);
    }
}
add_action('created_category', 'rt_taxonomy_metadate', 10, 1);
add_action('edited_category', 'rt_taxonomy_metadate', 10, 1);



/**
 * 分类模板选择
 */
add_action('category_edit_form_fields', 'rt_edit_category_tpl', 10, 2);
function rt_edit_category_tpl($tag)
{
    $rt_cat_tpl = get_option('rt_cat_tpl_' . $tag->term_id);
    $checked_0 = $rt_cat_tpl == 0 ? 'checked="checked"' : '';
    $checked_1 = $rt_cat_tpl == 1 ? 'checked="checked"' : '';

    echo '<tr class="form-field">
            <th scope="row"><label for="cat-keywords">模版选择</label></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span>模版选择</span></legend>
                    <label>
                        <input type="radio" name="rt_cat_tpl" value="0" ' . $checked_0 . '>
                        <span class="date-time-text">文章列表</span>&nbsp;&nbsp;<code>主要用来展示文字内容</code>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="rt_cat_tpl" value="1" ' . $checked_1 . '>
                        <span class="date-time-text">卡片列表</span>&nbsp;&nbsp;<code>主要用来展示图片内容</code>
                    </label>
                    <br>
                </fieldset>
            </td>
        </tr>';
}



function rt_add_cat_field()
{
    echo '<div class="form-field">
    <label for="cat-num">序号</label>
    <input name="_term_order" id="cat-num" type="text" value="" size="40">
    </div>';
}
add_action('category_add_form_fields', 'rt_add_cat_field', 10, 2);

// 分类编辑字段
function rt_edit_cat_field($tag)
{
    echo '<tr class="form-field">
    <th scope="row"><label for="cat-num">排序</label></th>
    <td>
    <input name="_term_order" id="cat-num" type="text" value="';
    echo (!empty($tag->term_group)) ? $tag->term_group : '0';
    echo '" size="40"/><br>
    </td>
    </tr>';
}
add_action('category_edit_form_fields', 'rt_edit_cat_field', 10, 2);

// 保存数据
function rt_tax_metadate($term_id)
{
    global $wpdb;
    if (isset($_POST['_term_order'])) {
        $wpdb->update($wpdb->terms, array('term_group' => $_POST['_term_order']), array('term_id' => $term_id));
    }
}

// 虽然要两个钩子，但是我们可以两个钩子使用同一个函数
add_action('created_category', 'rt_tax_metadate', 10, 1);
add_action('edited_category', 'rt_tax_metadate', 10, 1);


