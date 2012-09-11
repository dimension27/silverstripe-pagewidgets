<?php
/**
 * Image Widget. Only shows an image.
 */
class ImageWidget extends PageWidget {

	static $has_one = array(
		'Image' => 'BetterImage'
	);

	static $singular_name = 'Image Widget';

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Image', $field = new FileUploadField('Image'));
		PageWidget::set_upload_folder($field);
		return $fields;
	}

}
