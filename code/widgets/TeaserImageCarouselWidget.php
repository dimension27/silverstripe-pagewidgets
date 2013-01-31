<?php

class TeaserImageCarouselWidget extends ImageCarouselWidget {

	static $singular_name = 'Teaser Image Carousel Widget';
	static $item_class = 'TeaserImageCarouselItem';
	static $item_relation = 'TeaserImageCarouselItems';
	static $has_many = array(
		'Images' => 'TeaserImageCarouselItem'
	);

	public function Items() {
		$set = parent::Items();
		if( $this->Layout == 'OneCell' ) {
			foreach( $set as $item ) {
				$item->setImageSize(170, 80);
			}
		}
		return $set;
	}

}

class TeaserImageCarouselItem extends MultiTeaserImageBlockItem {

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName('ParentWidgetID');
		$fields->removeByName('ParentWidgetHelp');
		$fields->addFieldToTab('Root.Main', new TextareaField('Body'));
		return $fields;
	}

}