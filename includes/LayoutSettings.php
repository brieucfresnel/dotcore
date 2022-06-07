<?php

namespace DOT\Core;

if (!class_exists('LayoutSettings')) {
    class LayoutSettings {
        /**
         * @var string
         */
        public static string $group_key = 'group_layout_settings';

        /**
         * @var bool
         */
        private bool $is_preview;

        /**
         * @var array
         */
        private array $settings;

        /**
         * @var int
         */
        private int $layout_index;


        public function __construct() {
            $this->layout_index = 1;

            add_action('acfe/init', array($this, 'create_field_group'));
            add_action(
                "acfe/flexible/render/before_template/name=" . MainFlexible::$group_name,
                array($this, 'before_template'),
                10, 3
            );
            add_action('acfe/flexible/render/after_template', array($this, 'after_template'), 10, 3);
        }


        /**
         * Create field groups for layout settings
         * @return void
         */
        public function create_field_group() {
            acf_add_local_field_group(array(
                'key' => self::$group_key,
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
         * Echo layout header and container before if needed
         *
         * @param $field
         * @param $layout
         * @param $is_preview
         * @return void
         */
        public function before_template($field, $layout, $is_preview) {
            if (!acf_maybe_get($layout, 'dot_is_layout'))
                return;

            $this->is_preview = $is_preview;

            // TODO : Find a more elegant way to get settings
            $settings = get_sub_field($layout['sub_fields'][0]['key']);
            $this->settings = $settings;

            echo $this->get_header();
            echo $this->get_container();
        }

        public function after_template() {
            echo $this->get_footer();
        }

        /**
         * Get layout header template
         *
         * @return string
         */
        private function get_header(): string {
            $classes = array('layout');
            $classes[] = 'f-' . str_replace('_', '-', get_row_layout());
            if ($this->is_preview) {
                $classes[] = 'is-preview';
            }

            if (array_key_exists('bg_color', $this->settings) && $this->settings['bg_color'] !== 'none') {
                $classes[] = 'bg-' . $this->settings['bg_color'];
            }

            $bg_image = '';
            if (array_key_exists('bg_image', $this->settings) && is_array($this->settings['bg_image'])) {
                $bg_image = 'style="--bg-image: url(\'' . $this->settings['bg_image']['url'] . '\');"';
            }

            $id = !empty($this->settings['anchor_id']) ? sanitize_title_with_dashes($this->settings['anchor_id']) : 'layout-' . $this->layout_index;

            $header_html = '<section class="' . join(' ', $classes) . '"' . $bg_image;

            if (!empty($id))
                $header_html .= ' id="' . $id . '"';

            $header_html .= '>';

            $this->layout_index++;
            return $header_html;
        }

        /**
         * Get layout footer template
         *
         * @return string
         */
        private function get_footer() {
            return '</div></section>';
        }

        /**
         * Get layout container template
         *
         * @return string
         */
        private function get_container() {
            return $this->settings['contained'] === true ?
                '<div class="container">' :
                '<div class="fluid-container">';
        }
    }
}