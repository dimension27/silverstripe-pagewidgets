<?php

abstract class CarouselWidget extends PageWidget {

	static $singular_name = 'Carousel Widget';
	static $item_class = null;
	static $item_relation = 'MultiTeaserImageBlockItems';
	static $has_many = null;
	static $db = array(
		'Layout' => 'Enum("OneCell")',
	);

	/**
	 * @var DataObjectSet
	 */
	protected $items;

	/**
	 * @var DataObjectSet
	 */
	protected $cachedItems;

	/**
	 * @var string
	 */
	protected $header = false;

	public $addRowSpan = 0;

	function CSSClasses() {
		return 'carousel slide '.parent::CSSClasses();
	}

	public function allowCreate() {
		return false;
	}

	/**
	 * @return FieldSet
	 */
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$plural_name = singleton($this->stat('item_class'))->plural_name();
		$fields->addFieldToTab('Root.Items', $field = new HelpField(null,
				'The items inside the carousel are managed through the page form. '
				.'To add and remove items close this popup and go to the "'.$plural_name
				.'" tab. If you don\'t see this tab then you may need to Save the page first.'
		));
		$fields->addFieldsToTab('Root.Main', $field = new OptionsetField('Layout', 'Layout'));
		$field->setSource(array(
			'OneCell' => '1 grid cell',
		));
		return $fields;
	}

	/**
	 * @return MultiTeaserBlockItem
	 */
	public function Items() {
		Requirements::javascript('mysite/js/bootstrap.min.js');
		Requirements::css('mysite/css/bootstrap/css/bootstrap.css');
		$itemRelation = $this->stat('item_relation');
		$set = ($this->items ? $this->items : $this->Page()->$itemRelation()); /* @var $set DataObjectSet */
		$set->setPageLimits((int) @$_GET['start'], 10, 10);
		if( $this->Page()->$itemRelation('OpenInLightbox = 1', null, null, 1) ) {
			MediaPage_Controller::add_lightbox_requirements();
		}
		return $set;
	}

	/**
	 * @param DataObjectSet $items
	 */
	public function setItems( DataObjectSet $items ) {
		$this->items = $items;
		unset($this->cachedItems);
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
