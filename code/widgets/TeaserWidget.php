<?php
/**
 * Simple Teaser
 */
class TeaserWidget extends PageWidget {

	static $db = array(
		'Title' => 'Varchar',
		'Body' => 'HTMLText',
		'LinkLabel' => 'Varchar',
		'LinkType' => 'Enum("Internal, External, File")',
		'LinkTargetURL' => 'Varchar(255)',
		'OpenInLightbox' => 'Boolean',
	);

	static $has_one = array(
		'LinkTarget' => 'SiteTree',
		'LinkFile' => 'File'
	);

	static $char_limit = 50;
	static $singular_name = 'Simple Teaser';

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Main', new TextareaField('Body'), 'Row');
		PageWidget::add_link_fields($fields);
		return $fields;
	}

}

/**
 * Teaser with an images
 */
class TeaserImageWidget extends TeaserWidget {

	static $has_one = array(
		'Image' => 'BetterImage'
	);

	static $char_limit = 70;
	static $singular_name = 'Simple Teaser with Thumbnail';

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Image', $field = new FileUploadField('Image'));
		self::set_upload_folder($field);
		return $fields;
	}

}
