<?php

class TeaserImageCarouselWidget extends ImageCarouselWidget {

	static $item_class = 'TeaserImageCarouselItem';
	static $has_many = array(
		'Images' => 'TeaserImageCarouselItem'
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

class TeaserImageCarouselItem extends TeaserImageWidget {

	static $has_one = array(
		'Page' => 'Page'
	);

}