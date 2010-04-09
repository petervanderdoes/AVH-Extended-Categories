<?php
class AVH_EC_Widget_Helper_Class {

	function doFormText($field_id, $field_name, $description, $value) {
		echo '<label for="' . $field_id . '">';
		echo $description;
		echo '<input class="widefat" id="' . $field_id . '" name="' . $field_name . '" type="text" value="' . esc_attr($value) . '" /> ';
		echo '</label>';
		echo '<br />';
	}

	function doFormCheckbox($field_id,$field_name,$description,  $value) {
		echo '<label for="' . $field_id . '">';
		echo '<input class="checkbox" type="checkbox" id="' . $field_id . '"	name="' . $field_name . '" ' . $value . ' /> ';
		echo $description;
		echo '</label>';
		echo '<br />';
	}

	function doFormSelect($field_id,$field_name,$description,$options,$selected) {
		echo '<label for="' . $field_id . '">';
		echo $description.' ';
		echo '</label>';

		$data = '';
		foreach ($options as $value=>$text) {
			$data .= '<option value="'.$value.'" ' . ( $value == $selected ? "selected='selected'" : '') . '>' . $text . '</option>'."/n";
		}
		echo '<select id="' . $field_id . '" name="' . $field_name . '"> ';
		echo $data;
		echo '</select>';
		echo '<br />';
	}
}