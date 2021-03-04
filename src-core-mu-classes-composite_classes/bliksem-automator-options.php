<?php


namespace Bliksem_Uncanny_Automator;

/**
 * Class Automator_Options
 * @package Uncanny_Automator
 * @deprecated since v2.1.0
 */
class Automator_Options
{

    /*
     * Check for loading options.
     */
    protected $load_option = false;

    public function __construct()
    {
        if ($this->is_edit_page() || $this->is_automator_ajax()) {
            $this->load_option = true;
        }
    }

    /**
     * @return bool
     */
    public function is_automator_ajax()
    {
        if (! $this->is_ajax()) {
            return false;
        }

        //#10488 - ticket fix
        $ignore_actions = [
            'activity_filter',
            'bp_spam_activity',
            'post_update',
            'bp_nouveau_get_activity_objects',
            'new_activity_comment',
            'delete_activity',
            'activity_clear_new_mentions',
            'activity_mark_unfav',
            'activity_mark_fav',
            'get_single_activity_content',
            'messages_search_recipients',
            'messages_dismiss_sitewide_notice',
            'messages_read',
            'messages_unread',
            'messages_star',
            'messages_unstar',
            'messages_delete',
            'messages_get_thread_messages',
            'messages_thread_read',
            'messages_get_user_message_threads',
            'messages_send_reply',
            'messages_send_message',
            'groups_filter',
            'gamipress_track_visit',
        ];

        //Provide a filter for future use
        $ignore_actions = apply_filters('automator_post_actions_ignore_list', $ignore_actions);

        if (isset($_POST['action']) && isset($_POST['nonce'])) {
            if (in_array($_POST['action'], $ignore_actions)) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function is_ajax()
    {
        return function_exists('wp_doing_ajax') ? wp_doing_ajax() : defined('DOING_AJAX');
    }

    /**
     * is_edit_page
     * function to check if the current page is a post edit page
     *
     * @param string $new_edit what page to check for accepts new - new post page ,edit - edit post page, null for either
     *
     * @return boolean
     */
    public function is_edit_page($new_edit = null)
    {
        global $pagenow;

        if (null === $pagenow && isset($_SERVER['SCRIPT_FILENAME'])) {
            $pagenow = basename($_SERVER['SCRIPT_FILENAME']);
        }
        //make sure we are on the backend
        if (! is_admin()) {
            return false;
        }
        if (isset($_GET['post']) && ! empty($_GET['post'])) {
            $current_post = get_post(absint($_GET['post']));
            if (isset($current_post->post_type) && 'uo-recipe' === $current_post->post_type && in_array($pagenow, [
                    'post.php',
                    'post-new.php'
                ])) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * @param string $option_code
     * @param string $label
     * @param string $description
     * @param string $placeholder
     *
     * @return mixed
     */
    public function integer_field($option_code = 'INT', $label = null, $description = null, $placeholder = null)
    {
        if (! $label) {
            $label =  esc_attr__('Number', 'uncanny-automator');
        }

        if (! $description) {
            $description = '';
        }

        if (! $placeholder) {
            $placeholder =  esc_attr__('Example: 1', 'uncanny-automator');
        }

        $option = [
            'option_code' => $option_code,
            'label'       => $label,
            'description' => $description,
            'placeholder' => $placeholder,
            'input_type'  => 'int',
            'required'    => true,
        ];


        return apply_filters('uap_option_integer_field', $option);
    }

    /**
     * @param string $option_code
     * @param string $label
     * @param string $description
     * @param string $placeholder
     *
     * @return mixed
     */
    public function float_field($option_code = 'FLOAT', $label = null, $description = null, $placeholder = null)
    {
        if (! $label) {
            $label =  esc_attr__('Number', 'uncanny-automator');
        }

        if (! $description) {
            $description = '';
        }

        if (! $placeholder) {
            $placeholder =  esc_attr__('Example: 1.1', 'uncanny-automator');
        }

        $option = [
            'option_code' => $option_code,
            'label'       => $label,
            'description' => $description,
            'placeholder' => $placeholder,
            'input_type'  => 'float',
            'required'    => true,
        ];


        return apply_filters('uap_option_float_field', $option);
    }

    /**
     * @param string $option_code
     * @param string $label
     * @param bool $tokens
     * @param string $type
     * @param string $default
     * @param bool
     * @param string $description
     * @param string $placeholder
     *
     * @return mixed
     */
    public function text_field($option_code = 'TEXT', $label = null, $tokens = false, $type = 'text', $default = null, $required = true, $description = '', $placeholder = null)
    {
        if (! $label) {
            $label =  esc_attr__('Text', 'uncanny-automator');
        }

        if (! $description) {
            $description = '';
        }

        if (! $placeholder) {
            $placeholder = '';
        }

        $option = [
            'option_code'     => $option_code,
            'label'           => $label,
            'description'     => $description,
            'placeholder'     => $placeholder,
            'input_type'      => $type,
            'supports_tokens' => $tokens,
            'required'        => $required,
            'default_value'   => $default,
        ];

        if ('textarea' === $type) {
            $option['supports_tinymce'] = true;
        }


        return apply_filters('uap_option_text_field', $option);
    }


    /**
     * @param string $option_code
     * @param string $label
     * @param array $options
     * @param string $default
     * @param bool $is_ajax
     * @param string $fill_values_in
     *
     * @return mixed
     */
    public function select_field($option_code = 'SELECT', $label = null, $options = [], $default = null, $is_ajax = false, $fill_values_in = '', $relevant_tokens = [])
    {

        // TODO this function should be the main way to create select fields
        // TODO chained values should be introduced using the format in function "list_gravity_forms"
        // TODO the following function should use this function to create selections
        // -- less_or_greater_than
        // -- all_posts
        // -- all_pages
        // -- all_ld_courses
        // -- all_ld_lessons
        // -- all_ld_topics
        // -- all_ld_groups
        // -- all_ld_quiz
        // -- all_buddypress_groups
        // -- all_wc_products
        // -- list_contact_form7_forms
        // -- list_bbpress_forums
        // -- wc_order_statuses
        // -- wp_user_roles
        // -- list_gravity_forms
        // -- all_ec_events
        // -- all_lp_courses
        // -- all_lp_lessons
        // -- all_lf_courses
        // -- all_lf_lessons

        if (! $label) {
            $label =  esc_attr__('Option', 'uncanny-automator');
        }

        $option = [
            'option_code'     => $option_code,
            'label'           => $label,
            'input_type'      => 'select',
            'supports_tokens' => apply_filters('uap_option_' . $option_code . '_select_field', false),
            'required'        => true,
            'default_value'   => $default,
            'options'         => $options,
            //'is_ajax'         => $is_ajax,
            //'chained_to'      => $fill_values_in,
        ];

        if (! empty($relevant_tokens)) {
            $option['relevant_tokens'] = $relevant_tokens;
        }

        return apply_filters('uap_option_select_field', $option);
    }

    /**
     * @param string $option_code
     * @param string $label
     * @param array $options
     * @param string $default
     * @param bool $is_ajax
     *
     * @return mixed
     */
    public function select_field_ajax($option_code = 'SELECT', $label = null, $options = [], $default = null, $placeholder = '', $supports_token = false, $is_ajax = false, $args = [], $relevant_tokens = [])
    {


        // TODO this function should be the main way to create select fields
        // TODO chained values should be introduced using the format in function "list_gravity_forms"
        // TODO the following function should use this function to create selections
        // -- less_or_greater_than
        // -- all_posts
        // -- all_pages
        // -- all_ld_courses
        // -- all_ld_lessons
        // -- all_ld_topics
        // -- all_ld_groups
        // -- all_ld_quiz
        // -- all_buddypress_groups
        // -- all_wc_products
        // -- list_contact_form7_forms
        // -- list_bbpress_forums
        // -- wc_order_statuses
        // -- wp_user_roles
        // -- list_gravity_forms
        // -- all_ec_events
        // -- all_lp_courses
        // -- all_lp_lessons
        // -- all_lf_courses
        // -- all_lf_lessons

        if (! $label) {
            $label =  esc_attr__('Option', 'uncanny-automator');
        }

        $target_field = key_exists('target_field', $args) ? $args['target_field'] : '';
        $end_point    = key_exists('endpoint', $args) ? $args['endpoint'] : '';
        $supports_custom_value    = key_exists('supports_custom_value', $args) ? $args['supports_custom_value'] : '';

        $option = [
            'option_code'     => $option_code,
            'label'           => $label,
            'input_type'      => 'select',
            'supports_tokens' => apply_filters('uap_option_' . $option_code . '_select_field', false),
            'supports_custom_value' => $supports_custom_value,
            'required'        => true,
            'default_value'   => $default,
            'options'         => $options,
            'is_ajax'         => $is_ajax,
            'fill_values_in'  => $target_field,
            'integration'     => 'GF',
            'endpoint'        => $end_point,
            'placeholder'     => $placeholder,
        ];

        if (! empty($relevant_tokens)) {
            $option['relevant_tokens'] = $relevant_tokens;
        }

        return apply_filters('uap_option_select_field_ajax', $option);
    }

    /**
     * @param string $label
     * @param string $description
     * @param string $placeholder
     *
     * @return mixed
     */
    public function number_of_times($label = null, $description = null, $placeholder = null)
    {
        if (! $label) {
            $label =  esc_attr__('Number of times', 'uncanny-automator');
        }

        if (! $description) {
            $description = '';
        }

        if (! $placeholder) {
            $placeholder =  esc_attr__('Example: 1', 'uncanny-automator');
        }

        $option = [
            'option_code'   => 'NUMTIMES',
            'label'         => $label,
            'description'   => $description,
            'placeholder'   => $placeholder,
            'input_type'    => 'int',
            'default_value' => 1,
            'required'      => true,
        ];

        return apply_filters('uap_option_number_of_times', $option);
    }

    /**
     * @return mixed
     */
    public function less_or_greater_than()
    {
        $option = [
            'option_code' => 'NUMBERCOND',
            /* translators: Noun */
            'label'       =>  esc_attr__('Condition', 'uncanny-automator'),
            'input_type'  => 'select',
            'required'    => true,
            // 'default_value'      => false,
            'options'     => [
                '='  =>  esc_attr__('equal to', 'uncanny-automator'),
                '!=' =>  esc_attr__('not equal to', 'uncanny-automator'),
                '<'  =>  esc_attr__('less than', 'uncanny-automator'),
                '>'  =>  esc_attr__('greater than', 'uncanny-automator'),
                '>=' =>  esc_attr__('greater or equal to', 'uncanny-automator'),
                '<=' =>  esc_attr__('less or equal to', 'uncanny-automator'),
            ],
        ];

        return apply_filters('uap_option_less_or_greater_than', $option);
    }

    /**
     * @param string $label
     * @param string $option_code
     *
     * @return mixed
     */
    public function all_posts($label = null, $option_code = 'WPPOST', $any_option = true)
    {
        if (! $label) {
            /* translators: Noun */
            $label =  esc_attr__('Post', 'uncanny-automator');
        }

        $args = [
            'posts_per_page' => 999,
            'orderby'        => 'title',
            'order'          => 'DESC',
            'post_type'      => 'post',
            'post_status'    => 'publish',
        ];

        $all_posts = $this->wp_query($args, $any_option, esc_attr__('Any post', 'uncanny-automator'));

        $option = [
            'option_code'     => $option_code,
            'label'           => $label,
            'input_type'      => 'select',
            'required'        => true,
            'options'         => $all_posts,
            'relevant_tokens' => [
                $option_code          =>  esc_attr__('Post title', 'uncanny-automator'),
                $option_code . '_ID'  =>  esc_attr__('Post ID', 'uncanny-automator'),
                $option_code . '_URL' =>  esc_attr__('Post URL', 'uncanny-automator'),
            ],
        ];

        return apply_filters('uap_option_all_posts', $option);
    }

    /**
     * @param string $label
     * @param string $option_code
     *
     * @return mixed
     */
    public function all_pages($label = null, $option_code = 'WPPAGE', $any_option = false)
    {
        if (! $label) {
            $label =  esc_attr__('Page', 'uncanny-automator');
        }

        $args = [
            'posts_per_page' => 999,
            'orderby'        => 'title',
            'order'          => 'DESC',
            'post_type'      => 'page',
            'post_status'    => 'publish',
        ];

        $all_pages = $this->wp_query($args, $any_option, esc_attr__('Any page', 'uncanny-automator'));

        $option = [
            'option_code'     => $option_code,
            'label'           => $label,
            'input_type'      => 'select',
            'required'        => true,
            'options'         => $all_pages,
            'relevant_tokens' => [
                $option_code          =>  esc_attr__('Page title', 'uncanny-automator'),
                $option_code . '_ID'  =>  esc_attr__('Page ID', 'uncanny-automator'),
                $option_code . '_URL' =>  esc_attr__('Page URL', 'uncanny-automator'),
            ],
        ];

        return apply_filters('uap_option_all_pages', $option);
    }

    /**
     * @param string $label
     * @param string $option_code
     * @param array $args
     *
     * @return mixed
     */
    public function all_memberpress_products_onetime($label = null, $option_code = 'MPPRODUCT', $args = [])
    {
        if (! $label) {
            $label =  esc_attr__('Product', 'uncanny-automator');
        }

        $args = wp_parse_args(
            $args,
            array(
                'uo_include_any' => false,
                'uo_any_label'   =>  esc_attr__('Any one-time subscription product', 'uncanny-automator'),
            )
        );

        $options = [];
        if ($this->load_option) {
            if ($args['uo_include_any']) {
                $options[ - 1 ] = $args['uo_any_label'];
            }

            $posts = get_posts([
                'post_type'      => 'memberpressproduct',
                'posts_per_page' => 999,
                'post_status'    => 'publish',
                'meta_query'     => [
                    [
                        'key'     => '_mepr_product_period_type',
                        'value'   => 'lifetime',
                        'compare' => '=',
                    ]
                ]
            ]);

            if (! empty($posts)) {
                foreach ($posts as $post) {
                    $options[ $post->ID ] = $post->post_title;
                }
            }
        }
        $option = [
            'option_code'     => $option_code,
            'label'           => $label,
            'input_type'      => 'select',
            'required'        => true,
            'options'         => $options,
            'relevant_tokens' => [
                $option_code          =>  esc_attr__('Product title', 'uncanny-automator'),
                $option_code . '_ID'  =>  esc_attr__('Product ID', 'uncanny-automator'),
                $option_code . '_URL' =>  esc_attr__('Product URL', 'uncanny-automator'),
            ],
        ];


        return apply_filters('uap_option_all_memberpress_products_onetime', $option);
    }

    /**
     * @param string $label
     * @param string $option_code
     * @param array $args
     *
     * @return mixed
     */
    public function all_memberpress_products_recurring($label = null, $option_code = 'MPPRODUCT', $args = [])
    {
        if (! $label) {
            $label =  esc_attr__('Product', 'uncanny-automator');
        }

        $args = wp_parse_args(
            $args,
            array(
                'uo_include_any' => false,
                'uo_any_label'   =>  esc_attr__('Any recurring subscription product', 'uncanny-automator'),
            )
        );

        $options = [];
        if ($this->load_option) {
            if ($args['uo_include_any']) {
                $options[ - 1 ] = $args['uo_any_label'];
            }

            $posts = get_posts([
                'post_type'      => 'memberpressproduct',
                'posts_per_page' => 999,
                'post_status'    => 'publish',
                'meta_query'     => [
                    [
                        'key'     => '_mepr_product_period_type',
                        'value'   => 'lifetime',
                        'compare' => '!=',
                    ]
                ]
            ]);

            if (! empty($posts)) {
                foreach ($posts as $post) {
                    $options[ $post->ID ] = $post->post_title;
                }
            }
        }
        $option = [
            'option_code'     => $option_code,
            'label'           => $label,
            'input_type'      => 'select',
            'required'        => true,
            'options'         => $options,
            'relevant_tokens' => [
                $option_code          =>  esc_attr__('Product title', 'uncanny-automator'),
                $option_code . '_ID'  =>  esc_attr__('Product ID', 'uncanny-automator'),
                $option_code . '_URL' =>  esc_attr__('Product URL', 'uncanny-automator'),
            ],
        ];


        return apply_filters('uap_option_all_memberpress_products_recurring', $option);
    }

    /**
     * @param string $label
     * @param string $option_code
     * @param array $args
     *
     * @return mixed
     */
    public function all_memberpress_products($label = null, $option_code = 'MPPRODUCT', $args = [])
    {
        if (! $label) {
            $label =  esc_attr__('Product', 'uncanny-automator');
        }

        $args = wp_parse_args(
            $args,
            array(
                'uo_include_any' => false,
                'uo_any_label'   =>  esc_attr__('Any product', 'uncanny-automator'),
            )
        );

        $options = [];
        if ($this->load_option) {
            if ($args['uo_include_any']) {
                $options[ - 1 ] = $args['uo_any_label'];
            }

            $posts = get_posts([
                'post_type'      => 'memberpressproduct',
                'posts_per_page' => 999,
                'post_status'    => 'publish',
            ]);

            if (! empty($posts)) {
                foreach ($posts as $post) {
                    $options[ $post->ID ] = $post->post_title;
                }
            }
        }
        $option = [
            'option_code'     => $option_code,
            'label'           => $label,
            'input_type'      => 'select',
            'required'        => true,
            'options'         => $options,
            'relevant_tokens' => [
                $option_code          =>  esc_attr__('Product title', 'uncanny-automator'),
                $option_code . '_ID'  =>  esc_attr__('Product ID', 'uncanny-automator'),
                $option_code . '_URL' =>  esc_attr__('Product URL', 'uncanny-automator'),
            ],
        ];


        return apply_filters('uap_option_all_memberpress_products', $option);
    }
}