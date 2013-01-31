<?php

class CarouselWidget extends PageWidget {

	static $singular_name = 'Carousel Widget';
	static $item_class = null;
	static $item_relation = null;
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
		return 'carousel slide CarouselWidget '.parent::CSSClasses();
	}

	public function allowCreate() {
		// this is an 'abstract' class
		return get_class($this) != 'CarouselWidget';
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
		Requirements::javascript('pagewidget/js/bootstrap.min.js');
		Requirements::css('pagewidget/css/bootstrap-carousel.css');
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

class CarouselWidgetItem extends DataObject {

	static $has_one = array(
		'Page' => 'Page'
	);

	static $db = array(
		'Title' => 'Text',
	);

	public function getCMSFields() {
		$fields = FormUtils::createMain();
		$fields->addFieldToTab('Root.Main', $field = new TextField('Title'));
		return $fields;
	}

}
