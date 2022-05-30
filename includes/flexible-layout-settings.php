<?php

if (!class_exists('FlexibleLayoutSettings')) {
    class FlexibleLayoutSettings {
        /**
         * @var FlexibleLayoutSettings
         */
        private static FlexibleLayoutSettings $_instance;

        private array $field;
        private bool $is_preview;

        private array $settings;
        private array $layout;
        private int $layout_index;

        /**
         * @return FlexibleLayoutSettings
         */
        public static function instance(): FlexibleLayoutSettings {
            if (!isset(self::$_instance)) {
                self::$_instance = new FlexibleLayoutSettings();
            }

            return self::$_instance;
        }

        public function __construct() {
            $this->layout_index = 1;

            add_action(
                "acfe/flexible/render/before_template/name=" . MainFlexible::$name,
                array($this, 'before_template'), 10, 3);
            add_action('acfe/flexible/render/after_template', array($this, 'after_template'), 10, 3);
        }

        public function before_template($field, $layout, $is_preview) {
            if (!acf_maybe_get($layout, 'dot_is_layout'))
                return;

            $this->field = $field;
            $this->layout = $layout;
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
         * @return string
         */
        public function get_header(): string {
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
            // Header
            if (!empty($this->settings['title'])) {
                $header_html .= '<header class="layout__header"><div class="container">';
                $header_html .= '<h2 class="layout__title">' . esc_html($this->settings['title']) . '</h2>';
                $header_html .= '</div></header>';
            }

            $this->layout_index++;
            return $header_html;
        }

        public function get_footer() {
            return '</div></section>';
        }

        public function get_container() {
            return $this->settings['contained'] === true
                ? '<div class="container">'
                : '<div class="fluid-container">';
        }
    }

    FlexibleLayoutSettings::instance();
}