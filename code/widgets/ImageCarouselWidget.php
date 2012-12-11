<?php

class ImageCarouselWidget extends CarouselWidget {

	static $singular_name = 'Image Carousel Widget';
	static $item_class = 'ImageCarouselWidgetItem';
	static $item_relation = 'ImageCarouselWidgetItems';
	static $has_many = array(
		'Images' => 'ImageCarouselWidgetItem'
	);

	public function Items() {
		$set = parent::Items();
		if( $this->Layout == 'OneCell' ) {
			foreach( $set as $item ) {
				$item->setImageSize(170, 170);
			}
		}
		return $set;
	}

}

class ImageCarouselWidgetItem extends CarouselWidgetItem {

	static $has_one = array(
		'Image' => 'BetterImage',
		'LinkTarget' => 'SiteTree',
		'LinkFile' => 'File',
	);

	static $db = array(
		'Title' => 'Varchar',
		'LinkLabel' => 'Varchar',
		'LinkType' => 'Enum("Internal, External, File")',
		'LinkTargetURL' => 'Varchar(255)',
		'OpenInLightbox' => 'Boolean',
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Image', $field = new FileUploadField('Image'));
		PageWidget::set_upload_folder($field);
		PageWidget::add_link_fields($fields);
		return $fields;
	}

}
