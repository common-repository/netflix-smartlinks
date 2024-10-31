<?php
/*
Plugin Name: Netflix SmartLinks Widget
Description: Adds a sidebar widget to display Netflix SmartLinks feeds
Author: AdaptiveBlue, Inc.
Version: 1.0
Author URI: http://adaptiveblue.com
*/


// Put functions into one big function we'll call at the plugins_loaded
// action. This ensures that all required plugin functions are defined.
function widget_netflix_smartlinks_init() {

	// Check for the required plugin functions. This will prevent fatal
	// errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	// This is the function that outputs our little Google search form.
	function widget_netflix_smartlinks($args) {

		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);

	        // get netflix feed
		$options = get_option('widget_netflix_smartlinks');
		$feed = $options['feed'];
		$float = $options['float'];
		$width = $options['width'];
		$numItems = $options['numItems'];
		$amazonId = $options['amazonId'];
		$ebayId = $options['ebayId'];
		$googleId = $options['googleId'];
		echo $before_widget;
		echo widget_netflix_smartlinks_createScript($feed, $title, $numItems, $width, $float, $amazonId, $ebayId, $googleId);
		echo $after_widget;
	}

	function widget_netflix_smartlinks_control() {

		// Get our options and see if we're handling a form submission.
		$options = get_option('widget_netflix_smartlinks');
		if ( !is_array($options) )
		  $options = array('feed'=>__('http://rss.netflix.com/Top100RSS', 'widgets'), 'float'=>__('none', 'widgets'), 'numItems'=>__('3', 'widgets'), 'amazonId'=>__('', 'widgets'), 'ebayId'=>__('', 'widgets'), 'googleId'=>__('', 'widgets'), 'width'=>__('200', 'widgets'));
		if ( $_POST['netflix_smartlinks-submit'] ) {

			// Remember to sanitize and format use input appropriately.
			$options['feed'] = strip_tags(stripslashes($_POST['netflix_smartlinks-feed']));
			$options['float'] = strip_tags(stripslashes($_POST['netflix_smartlinks-float']));
			$options['width'] = strip_tags(stripslashes($_POST['netflix_smartlinks-width']));
			$options['numItems'] = strip_tags(stripslashes($_POST['netflix_smartlinks-numItems']));
			$options['amazonId'] = strip_tags(stripslashes($_POST['netflix_smartlinks-amazonId']));
			$options['ebayId'] = strip_tags(stripslashes($_POST['netflix_smartlinks-ebayId']));
			$options['googleId'] = strip_tags(stripslashes($_POST['netflix_smartlinks-googleId']));
			update_option('widget_netflix_smartlinks', $options);
		}

		// Be sure you format your options to be valid HTML attributes.
		$feed = htmlspecialchars($options['feed']);
		$float = htmlspecialchars($options['float']);
		$width = htmlspecialchars($options['width']);
		$numItems = htmlspecialchars($options['numItems']);
		$amazonId = htmlspecialchars($options['amazonId']);
		$ebayId = htmlspecialchars($options['ebayId']);
		$googleId = htmlspecialchars($options['googleId']);

        if(empty($numItems)) {
            $numItems = 4;
        }

        if(empty($width)) {
            $width = 200;
        }
		
		// form
		echo '<table>';
        echo '<tr><td><label for="netflix_smartlinks-feed">' . __('NetFlix Feed:', 'widgets') . '</label></td><td> <input style="width: 200px;" id="netflix_smartlinks-feed" name="netflix_smartlinks-feed" type="text" value="'.$feed.'" /></td></tr>';
		echo '<tr><td><label for="netflix_smartlinks-width">' . __('Widget Width:', 'widgets') . '</label></td><td> <input style="width: 200px;" id="netflix_smartlinks-width" name="netflix_smartlinks-width" type="text" value="'.$width.'" /></td></tr>';
		echo '<tr><td><label for="netflix_smartlinks-numItems">' . __('Num Items:', 'widgets') . '</label></td><td> <input style="width: 200px;" id="netflix_smartlinks-numItems" name="netflix_smartlinks-numItems" type="text" value="'.$numItems.'" /></td></tr>';
		echo '<tr><td><label for="netflix_smartlinks-amazonId">' . __('Amazon Affiliate:', 'widgets') . '</label></td><td> <input style="width: 200px;" id="netflix_smartlinks-amazonId" name="netflix_smartlinks-amazonId" type="text" value="'.$amazonId.'" /></td></tr>';
		echo '<tr><td><label for="netflix_smartlinks-ebayId">' . __('eBay Affiliate:', 'widgets') . '</label></td><td> <input style="width: 200px;" id="netflix_smartlinks-ebayId" name="netflix_smartlinks-ebayId" type="text" value="'.$ebayId.'" /></td></tr>';
		echo '<tr><td><label for="netflix_smartlinks-googleId">' . __('Google Affiliate:', 'widgets') . '</label></td><td> <input style="width: 200px;" id="netflix_smartlinks-googleId" name="netflix_smartlinks-googleId" type="text" value="'.$googleId.'" /></td></tr>';
		echo '<tr><td><label for="netflix_smartlinks-float">' . __('Float (optional):', 'widgets') . '</label></td><td> <select id="netflix_smartlinks-float" name="netflix_smartlinks-float">' . widget_netflix_smartlinks_getFloatOptions($float) . '</select></td></tr>';
        echo '</table>';
        echo '<p>(The float option is necessary if your widget pushes the content of your sidebar to the bottom, or is stretched to the bottom of the sidebar)</p>';
		echo '<input type="submit" id="netflix_smartlinks-submit" name="netflix_smartlinks-submit" value="submit" />';
	}

	function widget_netflix_smartlinks_createScript($feed, $title, $numItems, $width, $float, $amazonId, $ebayId, $googleId) {
        if(empty($numItems)) {
            $numItems = 4;
        }

        if(empty($width)) {
            $width = 200;
        }

        $script = '<script src="http://' . widget_netflix_smartlinks_getHostName() . '/users/GenerateBlueLinks.php?skin=white&width=' . $width .'&display=both&numItems=' . $numItems . '&auto=yes&title=&xsl=netflix.xsl&feedUrl='.  $feed . '&layout=list&blueAmazonId=' . $amazonId . '&blueEbayId=' .$ebayId .'&blueGoogleId=' . $googleId .'" type="text/javascript"></script>';

	    if(strcmp($float, "left") === 0 || strcmp($float, "right") === 0) {
	        return '<div style="float:' . $float . '">' . $script . '</div><div style="clear:' . $float .'"></div>';
	    }
	    else {
	        return $script;
	    }
	}

	function widget_netflix_smartlinks_getHostName() {
        $serverIndex = rand(1, 10);
        return "s" . $serverIndex . ".smrtlnks.com";
	}

	function widget_netflix_smartlinks_getFloatOptions($float) {
	    $floatOptions = array("none", "right", "left");
	    $result = '';

		foreach($floatOptions as $floatOption){
		    $result .= '<option value="' . $floatOption . '"';
		    if(strcmp($float, $floatOption) ===0) {
		        $result .= ' selected ';
		    }
		    $result .= '>' . $floatOption . '</option>';
		}

		return $result;
	}

	register_sidebar_widget(array('NetFlix SmartLinks', 'widgets'), 'widget_netflix_smartlinks');
	register_widget_control(array('NetFlix SmartLinks', 'widgets'), 'widget_netflix_smartlinks_control', 320, 350);
}

add_action('widgets_init', 'widget_netflix_smartlinks_init');

?>