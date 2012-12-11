<?php

class ImageCarouselWidget extends CarouselWidget {

	static $item_class = 'ImageCarouselItem';
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

class ImageCarouselItem extends ImageWidget {

	static $has_one = array(
		'Page' => 'Page'
	);

}
