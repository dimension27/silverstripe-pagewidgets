<?php

class MultiTeaserImageBlockWidget extends MultiTeaserBlockWidget {

	static $singular_name = 'Multi-Teaser Image Block';
	static $item_class = 'MultiTeaserImageBlockItem';
	static $item_relation = 'MultiTeaserImageBlockItems';

	function CSSClasses() {
		return 'MultiTeaserBlockWidget '.parent::CSSClasses();
	}

	public function allowCreate() {
		return true;
	}


	/**
	 * @return MultiTeaserBlockItem
	 */
	public function Items() {
		$set = parent::Items();
		// two column layout uses square image
		if( $this->Layout == 'FourGridCellsTwoColumn' ) {
			foreach( $set as $item ) {
				$item->setImageSize(170, 170);
			}
		}
		elseif( $this->Layout == 'FourGridCells' ) {
			foreach( $set as $item ) {
				$item->setImageSize(350, 170);
			}
		}
		return $set;
	}

}

class MultiTeaserImageBlockItem extends MultiTeaserBlockItem {

	static $has_one = array(
			'Image' => 'BetterImage'
	);

	public $imageWidth;
	public $imageHeight;

	static $image_width = 170;
	static $image_height = 80;

	/**
	 * @return FieldSet
	 */
	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Image', $field = new FileUploadField('Image'));
		PageWidget::set_upload_folder($field, $this);
		return $fields;
	}

	/**
	 * @param $width
	 * @param $height
	 * @return CroppedImage
	 */
	function SizedImage( $width = null, $height = null ) {
		if( !$width ) {
			$width = $this->imageWidth ? $this->imageWidth : self::$image_width;
		}
		if( !$height ) {
			$height = $this->imageHeight ? $this->imageHeight : self::$image_height;
		}
		if( $image = $this->Image() ) {
			return $image->SetCroppedSize($width, $height);
		}
	}

	static function set_image_size( $width, $height ) {
		self::$image_width = $width;
		self::$image_height = $height;
	}

	function setImageSize( $width, $height ) {
		$this->imageWidth = $width;
		$this->imageHeight = $height;
	}

}