<?php
/**
 * Image Widget. Only shows an image.
 */
class ImageWidget extends PageWidget {

	static $has_one = array(
		'Image' => 'BetterImage'
	);

	static $singular_name = 'Image Widget';

	static $extensions = array('LinkFieldsDecorator');

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Image', $field = new FileUploadField('Image'));
		PageWidget::set_upload_folder($field);
		LinkFields::addLinkFields($fields, null, 'Root.Link');
		return $fields;
	}

	public function SizedImage( $width, $height ) {
		$rv = parent::SizedImage($width, $height);
		if( $this->LinkURL() && $rv ) {
			$rv = $this->Anchor($rv->forTemplate());
		}
		return $rv;
	}
}
