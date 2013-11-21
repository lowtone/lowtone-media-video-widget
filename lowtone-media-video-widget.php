<?php
/*
 * Plugin Name: Video Widget
 * Plugin URI: http://wordpress.lowtone.nl/media-video-widget
 * Description: Create a widget for videos.
 * Version: 1.0
 * Author: Lowtone <info@lowtone.nl>
 * Author URI: http://lowtone.nl
 * License: http://wordpress.lowtone.nl/license
 */
/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2013, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\plugins\lowtone\media\video\widget
 */

namespace lowtone\media\video\widget {

	use lowtone\content\packages\Package,
		lowtone\ui\forms\Form,
		lowtone\ui\forms\Input,
		lowtone\wp\sidebars\Sidebar,
		lowtone\wp\widgets\simple\Widget;

	// Includes
	
	if (!include_once WP_PLUGIN_DIR . "/lowtone-content/lowtone-content.php") 
		return trigger_error("Lowtone Content plugin is required", E_USER_ERROR) && false;

	$__i = Package::init(array(
			Package::INIT_PACKAGES => array("lowtone\\media\\video"),
			Package::INIT_SUCCESS => function() {

				add_action("widgets_init", function() {

					Widget::register(array(
							Widget::PROPERTY_ID => "lowtone_media_video_widget",
							Widget::PROPERTY_NAME => __("Video Widget", "lowtone_media_video_widget"),
							Widget::PROPERTY_FORM => function($instance) {
								$form = new Form();

								$form
									->appendChild(
										$form->createInput(Input::TYPE_TEXT, array(
												Input::PROPERTY_NAME => "title",
												Input::PROPERTY_LABEL => __("Title", "lowtone_media_video_widget")
											))
									)
									->setValues($instance);

								return $form;
							},
							Widget::PROPERTY_WIDGET => function($args, $instance, $widget) {
								if (NULL === ($lastVideo = lastVideo()))
									return;

								echo $args[Sidebar::PROPERTY_BEFORE_WIDGET];

								if (isset($instance["title"]) && ($title = trim($instance["title"])))
									echo $args[Sidebar::PROPERTY_BEFORE_TITLE] . apply_filters("widget_title", $title, $instance, $widget->id_base) . $args[Sidebar::PROPERTY_AFTER_TITLE];

								echo '<div class="video">' . 
									do_shortcode("[video id=$lastVideo width='100%']") . 
									'</div>';

								echo $args[Sidebar::PROPERTY_AFTER_WIDGET];
							}
						));

				});

				// Register textdomain

				add_action("plugins_loaded", function() {
					load_plugin_textdomain("lowtone_media_video_widget", false, basename(__DIR__) . "/assets/languages");
				});

			}
		));

	function lastVideo() {
		global $wpdb;

		$query = "SELECT `ID` FROM `$wpdb->posts`
			WHERE 'attachment' = `post_type`
				AND `post_status` != 'trash'
				AND `post_mime_type` REGEXP '^video/'
			ORDER BY `post_date` DESC";

		return $wpdb->get_var($query);
	}

}