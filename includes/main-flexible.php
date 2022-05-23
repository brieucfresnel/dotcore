<?php

if (!class_exists('MainFlexible')) {
    class MainFlexible {

        /**
         * @var MainFlexible
         */
        private static MainFlexible $_instance;

        /**
         * The main field group name
         *
         * @var string
         */
        public static string $name;

        /**
         * @var string
         */
        private string $main_group_key = 'group_dot_main_flexible';

        /**
         * @var string
         */
        private string $main_flexible_field_key = 'field_dot_main_flexible';

        /**
         * @var array
         */
        private array $layouts;

        /**
         * @var string
         */
        private string $settings_group_key = 'group_layout_settings';


        /**
         * @param string $name
         */
        public function __construct(string $name = 'dot_layouts') {
            self::$name = $name;

            add_action('acfe/init', array($this, 'load'));
        }

        public function load() {
            $this->create_layout_settings_field_group();
            $this->set_layouts();
            $this->create_main_group();
            $this->create_main_flexible_field();
        }

        /**
         * @return MainFlexible
         */
        public static function instance(): MainFlexible {
            if (!isset(self::$_instance)) {
                self::$_instance = new MainFlexible();
            }

            return self::$_instance;
        }

        public function create_main_flexible_field() {
            $config = array(
                'key' => $this->main_flexible_field_key,
                'label' => 'Dispositions',
                'name' => self::$name,
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
                'parent' => $this->main_group_key
            );

            acf_add_local_field($config);
            $this->main_flexible_field_config = $config;
        }

        /**
         * Creates ACF local field group that will contain the main flexible field
         *
         * @return void
         */
        public function create_main_group() {
            acf_add_local_field_group(array(
                'key' => $this->main_group_key,
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

        public function set_layouts() {
            $field_groups = acf_get_field_groups();
            $layouts = [];

            if (!$field_groups) {
                return;
            }

            foreach ($field_groups as $field_group) {
                if (str_contains($field_group['key'], 'acfe')
                    || $field_group['key'] === $this->main_group_key
                    || $field_group['key'] === $this->settings_group_key) {
                    continue;
                }

                $title = $field_group['title'];
                $name = sanitize_title($field_group['title']);
                $layout_slug = str_replace('-', '_', $name);
                $file_path = DOT_THEME_LAYOUTS_PATH . $name . '/';

                $render_layout = $file_path . $layout_slug . '.php';
                $render_script = $file_path . $layout_slug . '.js';
                $render_style = $file_path . $layout_slug . '.css';

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

                $is_layout = $field_group['dot_is_layout'];

                // Store layout
                $layouts[] = array(
                    'key' => 'layout_' . $layout_slug,
                    '_dot_layout_slug' => $layout_slug,
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
                    'dot_is_layout' => $is_layout,
                    'acfe_flexible_settings' => array(
                        0 => $this->settings_group_key,
                    ),
                );
            }

            $this->layouts = $layouts;
        }

        private function create_layout_settings_field_group() {
            acf_add_local_field_group(array(
                'key' => $this->settings_group_key,
                'title' => 'Paramètres de disposition',
                'fields' => array(
                    array(
                        'key' => 'field_container',
                        'label' => 'Ajouter un container ?',
                        'name' => 'contained',
                        'type' => 'true_false',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'message' => '',
                        'default_value' => 1,
                        'ui' => 0,
                        'ui_on_text' => '',
                        'ui_off_text' => '',
                    ),
                    array(
                        'key' => 'field_bg-image',
                        'label' => 'Image de fond',
                        'name' => 'bg_image',
                        'type' => 'image',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'uploader' => '',
                        'acfe_thumbnail' => 0,
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => '',
                    ),
                    array(
                        'key' => 'field_bg-color',
                        'label' => 'Couleur de fond',
                        'name' => 'bg_color',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'none' => 'Aucune',
                            'red' => 'Rouge',
                            'blue' => 'Bleu',
                            'lightgrey' => 'Gris clair',
                            'yellow' => 'Jaune',
                            'orange' => 'Orange',
                            'dark-orange' => 'Orange Sombre',
                        ),
                        'default_value' => 'none',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 0,
                        'return_format' => 'value',
                        'ajax' => 0,
                        'placeholder' => '',
                    ),
                    array(
                        'key' => 'field_anchor-id',
                        'label' => 'Module CSS ID',
                        'name' => 'anchor_id',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'post',
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
                    0 => 'php',
                    1 => 'json',
                ),
                'acfe_form' => 0,
                'acfe_meta' => '',
                'acfe_note' => '',
            ));
        }

        /**
         * @return string
         */
        public function get_name(): string {
            return self::$name;
        }
    }

    new MainFlexible();
}


function get_dot_layouts() {
    if (has_flexible('dot_layouts')):
        the_flexible('dot_layouts');
    endif;
}