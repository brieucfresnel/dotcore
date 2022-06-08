<?php

namespace DOT\Core;

if (!class_exists('MainFlexible')) {
    class MainFlexible {

        /**
         * The main field group name
         *
         * @var string
         */
        public static string $group_name = 'dot_layouts';

        /**
         * The main field group key
         *
         * @var string
         */
        public static string $group_key = 'group_main_flexible';

        /**
         * The main flexible field key
         *
         * @var string
         */
        private string $field_key = 'field_main_flexible';

        /**
         * The flexible layouts schemas
         *
         * @var array
         */
        private array $layouts;


        public function __construct() {
            add_action('init', array($this, 'load'), 10);
        }

        public function load() {
            $this->set_layouts();
            $this->create_main_group();
            $this->create_flexible_field();
        }

        /**
         * @return void
         */
        private function set_layouts() {
            $field_groups = acf_get_field_groups();
            $layouts = [];

            if (!$field_groups) {
                return;
            }

            foreach ($field_groups as $field_group) {
                if (!dot_is_layout($field_group)) {
                    continue;
                }

                $title = $field_group['title'];
                $name = sanitize_title($field_group['title']);
                $layout_slug = $field_group['dot_layout_slug'];
                $file_path = DOT_THEME_LAYOUTS_PATH . $name . '/';

                $render_layout = $file_path . $name . '.php';
                $render_script = $file_path . $name . '.js';
                $render_style = $file_path . $name . '.css';

                if (!file_exists($render_style)) {
                    $render_style = null;
                }

                // Check if JS file exists before enqueue
                if (!file_exists($render_script)) {
                    $render_script = null;
                }

                // Get layout alignment
                switch ($field_group['label_placement']) {
                    case 'top':
                        $display = 'block';
                        break;
                    case 'left':
                    default:
                        $display = 'row';
                        break;
                }

//                $categories = '';
//                if(!empty($field_group['dot_categories'])) {
//                    $categories = $field_group['dot_categories'];
//                }
//                dot_print_r($categories);
                $categories_terms = get_the_terms($field_group['ID'], 'acf-field-group-category');
                $acfe_categories = array();

                if(is_array($categories_terms)) {
                    foreach($categories_terms as $category) {
                        $acfe_categories[] = $category->name;
                    }
                }

                // Store layout
                $layouts[] = array(
                    'key' => 'group_' . $layout_slug,
                    'dot_layout_slug' => $layout_slug,
                    'dot_is_layout' => 1,
                    'name' => $name,
                    'label' => $title,
                    'display' => $display,
                    'sub_fields' => array(
                        array(
                            'key' => 'field_clone_' . $layout_slug,
                            'label' => $title,
                            'name' => $name,
                            'type' => 'clone',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'clone' => array(
                                $field_group['key'],
                            ),
                            'display' => 'seamless',
                            'layout' => 'block',
                            'prefix_label' => 0,
                            'prefix_name' => 0,

                        ),
                    ),
                    'acfe_flexible_render_template' => $render_layout,
                    'acfe_flexible_render_style' => $render_style,
                    'acfe_flexible_render_script' => $render_script,
                    'acfe_flexible_settings' => array(
                        0 => LayoutSettings::$group_key,
                    ),
                    'acfe_flexible_category' => $acfe_categories
                );
            }

            $this->layouts = $layouts;
        }

        /**
         * Create ACF local field group that will contain the main flexible field
         *
         * @return void
         */
        private function create_main_group() {
            acf_add_local_field_group(array(
                'key' => self::$group_key,
                'title' => 'Dispositions',
                'fields' => array(),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'page',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'left',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
                'show_in_rest' => 0,
                'acfe_display_title' => '',
                'acfe_autosync' => array(
                    1 => 'php',
                    1 => 'json',
                ),
                'acfe_form' => 0,
                'acfe_meta' => '',
                'acfe_note' => '',
                'modified' => 1647265579,
            ));
        }


        /**
         * Create main flexible field and assign it to main group
         *
         * @return void
         */
        private function create_flexible_field() {

            $config = array(
                'key' => $this->field_key,
                'label' => 'Layouts',
                'name' => self::$group_name,
                'type' => 'flexible_content',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'acfe_flexible_layouts_previews' => 1,
                'acfe_flexible_settings_size' => 'medium',
                'acfe_flexible_advanced' => 1,
                'acfe_flexible_stylised_button' => 1,
                'acfe_flexible_layouts_templates' => 1,
                'acfe_flexible_layouts_placeholder' => 1,
                'acfe_flexible_layouts_thumbnails' => 1,
                'acfe_flexible_layouts_settings' => 1,
                'acfe_flexible_async' => array(),
                'acfe_flexible_add_actions' => array(),
                'acfe_flexible_remove_button' => array(),
                'acfe_flexible_layouts_state' => 'user',
                'acfe_flexible_modal_edit' => array(
                    'acfe_flexible_modal_edit_enabled' => '0',
                    'acfe_flexible_modal_edit_size' => 'large',
                ),
                'acfe_flexible_modal' => array(
                    'acfe_flexible_modal_enabled' => '1',
                    'acfe_flexible_modal_title' => 'Ajouter une disposition',
                    'acfe_flexible_modal_size' => 'full',
                    'acfe_flexible_modal_col' => '4',
                    'acfe_flexible_modal_categories' => '1',
                ),
                'layouts' => $this->layouts,
                'parent' => self::$group_key
            );

            acf_add_local_field($config);
        }

        public function get_field_key(): string {
            return $this->field_key;
        }
    }
}