<?php
/*
Plugin Name: Elementor Particle.js Background
Description: Adds Particle.js background option to Elementor sections and containers
Version: 1.0
Author: Arijit Hati
*/

if (!defined('ABSPATH')) exit; // 

class Elementor_ParticleJS_Background {

    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('elementor/element/section/section_background/before_section_end', [$this, 'add_controls_to_section'], 10, 2);
        add_action('elementor/element/container/section_background/before_section_end', [$this, 'add_controls_to_section'], 10, 2);
        add_action('elementor/frontend/section/before_render', [$this, 'before_render']);
        add_action('elementor/frontend/container/before_render', [$this, 'before_render']);
    }

    public function enqueue_scripts() {
        wp_enqueue_script('particles-js', 'https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js', [], '2.0.0', true);
        wp_enqueue_script('elementor-particlejs-background', plugins_url('assets/js/particlejs-background.js', __FILE__), ['jquery', 'particles-js'], '1.2', true);
        wp_enqueue_style('elementor-particlejs-background', plugins_url('assets/css/particlejs-background.css', __FILE__), [], '1.2');
    }

    public function add_controls_to_section($element, $args) {
        $element->add_control(
            'particlejs_background_enable',
            [
                'label' => __('Enable Particle.js Background', 'elementor-particlejs-background'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', 'elementor-particlejs-background'),
                'label_off' => __('No', 'elementor-particlejs-background'),
                'return_value' => 'yes',
            ]
        );

        $element->add_control(
            'particlejs_color',
            [
                'label' => __('Particles Color', 'elementor-particlejs-background'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'condition' => [
                    'particlejs_background_enable' => 'yes',
                ],
            ]
        );

        $element->add_control(
            'particlejs_number',
            [
                'label' => __('Number of Particles', 'elementor-particlejs-background'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 80,
                'min' => 1,
                'max' => 500,
                'step' => 1,
                'condition' => [
                    'particlejs_background_enable' => 'yes',
                ],
            ]
        );

        $element->add_control(
            'particlejs_size',
            [
                'label' => __('Particle Size', 'elementor-particlejs-background'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 3,
                ],
                'condition' => [
                    'particlejs_background_enable' => 'yes',
                ],
            ]
        );
    }

    public function before_render($element) {
    $settings = $element->get_settings_for_display();

    if (isset($settings['particlejs_background_enable']) && $settings['particlejs_background_enable'] === 'yes') {
        $element->add_render_attribute('_wrapper', 'class', 'has-particlejs-background');
        $element->add_render_attribute('_wrapper', 'data-particlejs-color', $settings['particlejs_color']);
        $element->add_render_attribute('_wrapper', 'data-particlejs-number', $settings['particlejs_number']);
        $element->add_render_attribute('_wrapper', 'data-particlejs-size', $settings['particlejs_size']['size']);
        $element->add_render_attribute('_wrapper', 'data-particlejs-id', 'particlejs-' . $element->get_id());
    }
}
}

Elementor_ParticleJS_Background::instance();