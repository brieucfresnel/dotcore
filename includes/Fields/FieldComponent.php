<?php

namespace DOT\Core\Fields;


use DOT\Core\Components;

class FieldComponent extends \acf_field {


    /*
    *  __construct
    *
    *  This function will setup the field type data
    *
    *  @type    function
    *  @date    5/03/2014
    *  @since   5.0.0
    *
    *  @param   n/a
    *  @return  n/a
    */

    function initialize() {

        // vars
        $this->name = 'component';
        $this->label = __('Component', 'acf');
        $this->category = 'relational';
        $this->defaults = array(
            'post_type' => Components::$post_type,
            'return_format' => 'object',
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 1,
            'taxonomy' => array(),
        );

        // extra
        add_action('wp_ajax_acf/fields/component/query', array($this, 'ajax_query'));
        add_action('wp_ajax_nopriv_acf/fields/component/query', array($this, 'ajax_query'));

    }


    /*
    *  ajax_query
    *
    *  description
    *
    *  @type    function
    *  @date    24/10/13
    *  @since   5.0.0
    *
    *  @param   $post_id (int)
    *  @return  $post_id (int)
    */

    function ajax_query() {

        // validate
        if (!acf_verify_ajax()) {
            die();
        }

        // get choices
        $response = $this->get_ajax_query($_POST);

        // return
        acf_send_ajax_results($response);

    }


    /*
    *  get_ajax_query
    *
    *  This function will return an array of data formatted for use in a select2 AJAX response
    *
    *  @type    function
    *  @date    15/10/2014
    *  @since   5.0.9
    *
    *  @param   $options (array)
    *  @return  (array)
    */

    function get_ajax_query($options = array()) {
        // defaults
        $options = acf_parse_args(
            $options,
            array(
                'post_id' => 0,
                's' => '',
                'field_key' => '',
                'paged' => 1,
            )
        );

        // load field
        $field = acf_get_field($options['field_key']);
        if (!$field) {
            return false;
        }

        // vars
        $results = array();
        $args = array();
        $s = false;
        $is_search = false;

        // paged
        $args['posts_per_page'] = 20;
        $args['paged'] = $options['paged'];

        // search
        if ($options['s'] !== '') {

            // strip slashes (search may be integer)
            $s = wp_unslash(strval($options['s']));

            // update vars
            $args['s'] = $s;
            $is_search = true;

        }

        $args['post_type'] = Components::$post_type;

        // get posts grouped by post type
        $components = dot_get_components($args);

        // bail early if no posts
        if (empty($components)) {
            return false;
        }

        // loop
        foreach ($components as $component) {

            // data
            $data = array(
                'text' => $component->title,
                'id' => $component->ID
            );
            // append to $results
            $results[] = $data;

        }

        // vars
        $response = array(
            'results' => $results,
            'limit' => $args['posts_per_page'],
        );

        return $response;

    }


    /*
    *  get_post_result
    *
    *  This function will return an array containing id, text and maybe description data
    *
    *  @type    function
    *  @date    7/07/2016
    *  @since   5.4.0
    *
    *  @param   $id (mixed)
    *  @param   $text (string)
    *  @return  (array)
    */

    function get_post_result($id, $text) {

        // vars
        $result = array(
            'id' => $id,
            'text' => $text,
        );

        // look for parent
        $search = '| ' . __('Parent', 'acf') . ':';
        $pos = strpos($text, $search);

        if ($pos !== false) {

            $result['description'] = substr($text, $pos + 2);
            $result['text'] = substr($text, 0, $pos);

        }

        // return
        return $result;

    }


    /*
    *  get_post_title
    *
    *  This function returns the HTML for a result
    *
    *  @type    function
    *  @date    1/11/2013
    *  @since   5.0.0
    *
    *  @param   $post (object)
    *  @param   $field (array)
    *  @param   $post_id (int) the post_id to which this value is saved to
    *  @return  (string)
    */

    function get_post_title($post, $field, $post_id = 0, $is_search = 0) {

        // get post_id
        if (!$post_id) {
            $post_id = acf_get_form_data('post_id');
        }

        // vars
        $title = acf_get_post_title($post, $is_search);

        // filters
        $title = apply_filters('acf/fields/component/result', $title, $post, $field, $post_id);
        $title = apply_filters('acf/fields/component/result/name=' . $field['_name'], $title, $post, $field, $post_id);
        $title = apply_filters('acf/fields/component/result/key=' . $field['key'], $title, $post, $field, $post_id);

        // return
        return $title;
    }


    /*
    *  render_field()
    *
    *  Create the HTML interface for your field
    *
    *  @param   $field - an array holding all the field's data
    *
    *  @type    action
    *  @since   3.6
    *  @date    23/01/13
    */

    function render_field($field) {

        // Change Field into a select
        $field['type'] = 'select';
        $field['ui'] = 1;
        $field['ajax'] = 1;
        $field['choices'] = array();

        // load posts
        $posts = $this->get_posts($field['value'], $field);

        if ($posts) {

            foreach (array_keys($posts) as $i) {

                // vars
                $post = acf_extract_var($posts, $i);

                // append to choices
                $field['choices'][$post->ID] = $this->get_post_title($post, $field);

            }
        }

        // render
        acf_render_field($field);

    }


    /*
    *  render_field_settings()
    *
    *  Create extra options for your field. This is rendered when editing a field.
    *  The value of $field['name'] can be used (like bellow) to save extra data to the $field
    *
    *  @type    action
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $field  - an array holding all the field's data
    */

    function render_field_settings($field) {

    }


    /*
    *  load_value()
    *
    *  This filter is applied to the $value after it is loaded from the db
    *
    *  @type    filter
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $value (mixed) the value found in the database
    *  @param   $post_id (mixed) the $post_id from which the value was loaded
    *  @param   $field (array) the field array holding all the field options
    *  @return  $value
    */

    function load_value($value, $post_id, $field) {

        // ACF4 null
        if ($value === 'null') {
            return false;
        }

        // return
        return $value;

    }


    /*
    *  format_value()
    *
    *  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
    *
    *  @type    filter
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $value (mixed) the value which was loaded from the database
    *  @param   $post_id (mixed) the $post_id from which the value was loaded
    *  @param   $field (array) the field array holding all the field options
    *
    *  @return  $value (mixed) the modified value
    */

    function format_value($value, $post_id, $field) {

        // numeric
        $value = acf_get_numeric($value);

        // bail early if no value
        if (empty($value)) {
            return false;
        }

        // load posts if needed
        if ($field['return_format'] == 'object') {

            $value = $this->get_posts($value, $field);

        }

        // return value
        return $value;

    }


    /*
    *  update_value()
    *
    *  This filter is appied to the $value before it is updated in the db
    *
    *  @type    filter
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $value - the value which will be saved in the database
    *  @param   $post_id - the $post_id of which the value will be saved
    *  @param   $field - the field array holding all the field options
    *
    *  @return  $value - the modified value
    */

    function update_value($value, $post_id, $field) {

        // Bail early if no value.
        if (empty($value)) {
            return $value;
        }

        // Format array of values.
        // - ensure each value is an id.
        // - Parse each id as string for SQL LIKE queries.
        if (acf_is_sequential_array($value)) {
            $value = array_map('acf_idval', $value);
            $value = array_map('strval', $value);

            // Parse single value for id.
        } else {
            $value = acf_idval($value);
        }

        // Return value.
        return $value;
    }


    /*
    *  get_posts
    *
    *  This function will return an array of posts for a given field value
    *
    *  @type    function
    *  @date    13/06/2014
    *  @since   5.0.0
    *
    *  @param   $value (array)
    *  @return  $value
    */

    function get_posts($value, $field) {

        // get posts
        $posts = acf_get_posts(
            array(
                'post_type' => DOT\Core\Components::$post_type,
            )
        );

        dot_print_r($posts);
        // return
        return $posts;

    }

    /**
     * Validates post object fields updated via the REST API.
     *
     * @param bool $valid
     * @param int $value
     * @param array $field
     *
     * @return bool|WP_Error
     */
    public function validate_rest_value($valid, $value, $field) {
        if (is_null($value)) {
            return $valid;
        }

        $param = sprintf('%s[%s]', $field['prefix'], $field['name']);
        $data = array('param' => $param);
        $value = is_array($value) ? $value : array($value);

        $invalid_posts = array();
        $post_type_errors = array();
        $taxonomy_errors = array();

        foreach ($value as $post_id) {
            if (is_string($post_id)) {
                continue;
            }

            $post_type = get_post_type($post_id);
            if (!$post_type) {
                $invalid_posts[] = $post_id;
                continue;
            }

            if (
                is_array($field['post_type']) &&
                !empty($field['post_type']) &&
                !in_array($post_type, $field['post_type'])
            ) {
                $post_type_errors[] = $post_id;
            }

        }

        if (count($invalid_posts)) {
            $error = sprintf(
                __('%1$s must have a valid post ID.', 'acf'),
                $param
            );
            $data['value'] = $invalid_posts;
            return new WP_Error('rest_invalid_param', $error, $data);
        }

        if (count($post_type_errors)) {
            $error = sprintf(
                _n(
                    '%1$s must be of post type %2$s.',
                    '%1$s must be of one of the following post types: %2$s',
                    count($field['post_type']),
                    'acf'
                ),
                $param,
                count($field['post_type']) > 1 ? implode(', ', $field['post_type']) : $field['post_type'][0]
            );
            $data['value'] = $post_type_errors;

            return new WP_Error('rest_invalid_param', $error, $data);
        }

        if (count($taxonomy_errors)) {
            $error = sprintf(
                _n(
                    '%1$s must have term %2$s.',
                    '%1$s must have one of the following terms: %2$s',
                    count($field['taxonomy']),
                    'acf'
                ),
                $param,
                count($field['taxonomy']) > 1 ? implode(', ', $field['taxonomy']) : $field['taxonomy'][0]
            );
            $data['value'] = $taxonomy_errors;

            return new WP_Error('rest_invalid_param', $error, $data);
        }

        return $valid;
    }

    /**
     * Return the schema array for the REST API.
     *
     * @param array $field
     * @return array
     */
    public function get_rest_schema(array $field) {
        $schema = array(
            'type' => array('integer', 'array', 'null'),
            'required' => !empty($field['required']),
            'items' => array(
                'type' => 'integer',
            ),
            'minItems' => 1,
            'maxItems' => 1
        );

        return $schema;
    }

    /**
     * @param mixed $value The raw (unformatted) field value.
     * @param int|string $post_id
     * @param array $field
     * @return array
     * @see \acf_field::get_rest_links()
     */
    public function get_rest_links($value, $post_id, array $field) {
        $links = array();

        if (empty($value)) {
            return $links;
        }

        foreach ((array)$value as $object_id) {
            if (!$post_type = get_post_type($object_id)) {
                continue;
            }

            if (!$post_type_object = get_post_type_object($post_type)) {
                continue;
            }

            $rest_base = acf_get_object_type_rest_base($post_type_object);
            $links[] = array(
                'rel' => $post_type_object->name === 'attachment' ? 'acf:attachment' : 'acf:post',
                'href' => rest_url(sprintf('/wp/v2/%s/%s', $rest_base, $object_id)),
                'embeddable' => true,
            );
        }

        return $links;
    }

    /**
     * Apply basic formatting to prepare the value for default REST output.
     *
     * @param mixed $value
     * @param string|int $post_id
     * @param array $field
     * @return mixed
     */
    public function format_value_for_rest($value, $post_id, array $field) {
        return acf_format_numerics($value);
    }

}




