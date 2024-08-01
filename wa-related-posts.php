<?php
/**
 * Plugin Name: WA Related Posts
 * Plugin URI:  http://taslimhossain.com/plugins/wa-related-posts/
 * Description: A WordPress plugin for weDevs Academy WordPress plugin development course assignment.
 * Version:     1.0.0
 * Author:      taslim
 * Author URI:  https://taslimhossain.com/
 * Text Domain: wa-related-posts
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WA_Related_Posts class
 * 
 * @class WA_Related_Posts The class that holds the entire WA_Related_Posts plugin
 */
if ( ! class_exists( 'WA_Related_Posts' ) ) :
    class WA_Related_Posts {

        /**
         * Constructor for the WA_Related_Posts class
         * 
         * Sets up all the appropriate hooks and actions within our plugin.
         */
        public function __construct() {
            add_action( 'wp_head', array( $this, 'related_posts_css' ) );
            add_filter( 'the_content', array( $this, 'related_posts_list' ), 1, 10 );
        }

        /**
         * Initializes the WA_Related_Posts class
         * 
         * Checks for an existing WA_Related_Posts() instance
         * and if it doesn't find one, creates it.
         */
        public static function init() {
            static $instance = false;

            if( ! $instance ) {
                $instance = new self();
            }
            
            return $instance;
        }

        /**
         * Prints styles for product list.
         * @return void
         */
        function related_posts_css() {
            ?>
            <style type="text/css">
                .wa-related-posts-area ul {display: flex;flex-direction: column;flex-wrap: nowrap;row-gap: 10px;list-style: none;padding: 0;margin: 0;}
                .wa-related-posts-area ul li {border-bottom: 1px solid #bfbfbf; padding-bottom: 10px;}
                .wa-related-posts-area ul li:last-child {border-bottom: none; padding-bottom: 0px;}
                .wa-related-posts-area ul li a {display: flex;column-gap: 20px;flex-direction: row;font-size: 22px;text-decoration:none;}
                .wa-related-posts-area ul li .no-image { width: 80px; height: 80px; background-color: #cdcdcd; display: inline-block;}
            </style>
            <?php
        }

        /**
         * Fetch Related posts
         *
         * @return string
         */
        public function related_posts_list( $content ) {
            $categories = get_the_category();

            if ( $categories ) {
                $categorie_ids = wp_list_pluck( $categories, 'cat_ID' );

                $args = array(
                    'post_type'      => 'post',
                    'post_status'    => 'publish',
                    'posts_per_page' => 5,
                    'orderby'        => 'rand',
                    'post__not_in'   => array(get_the_ID()),
                    'category__in'   => $categorie_ids
                );
                
                ob_start();
                $related_posts = new WP_Query( $args );

                if ( $related_posts->have_posts() ) {
                    $content .= '<div class="wa-related-posts-area">';
                    $content .= '<h3>' . esc_html( __( 'Related Posts', 'wa-related-posts' ) ) . '</h3>';
                    $content .= '<ul>';
                    while ( $related_posts->have_posts() ) {
                        $related_posts->the_post();
                        $content .= '<li><a href="' . esc_url( get_the_permalink() ) . '">';
                        if ( has_post_thumbnail() ) {
                            $content .= get_the_post_thumbnail( null, array( 80, 80 ) );
                        } else {
                            $content .= '<span class="no-image"></span>';
                        }
                        $content .= esc_html( get_the_title() );
                        $content .= '</a></li>'; 
                    }
                    $content .= '</ul>';
                    $content .= '</div>';
                } //end have posts.

                // Reset post data
                wp_reset_postdata();

                return $content;
            } //end have categories.
        }

    }
endif;

/**
 * Returns the main instance of WA_Related_Posts to prevent the need to use globals
 *
 * @return \WA_Related_Posts
 */
if ( ! function_exists( 'wa_related_posts' ) ) {
    function wa_related_posts() {
        return WA_Related_Posts::init();
    }
}

// initialize the plugin
wa_related_posts();