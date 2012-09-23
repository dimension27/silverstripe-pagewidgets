<?php
/**
 * Contains Teaser Type Items
 */
class MultiTeaserBlockWidget extends PageWidget {

	static $singular_name = 'Multi Teaser Block';
	static $item_class = 'MultiTeaserBlockItem';
	static $item_relation = 'MultiTeaserBlockItems';

	static $has_many = array(
		'TeaserWidgets' => 'TeaserWidget'
	);

	static $db = array(
		'Layout' => 'Enum("TwoGridCells,ThreeGridCells,FourGridCells,FourGridCellsTwoColumn")',
		'ThreeColumnWidth' => 'Boolean',
		'TwoColumnLayout' => 'Boolean',
		'NumItemsPerPage' => 'Int',
	);

	static $defaults = array(
		'NumItemsPerPage' => 10
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

	/**
	 * @return FieldSet
	 */
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$plural_name = singleton($this->stat('item_class'))->plural_name();
		$fields->addFieldToTab('Root.Items', $field = new HelpField(null,
				'The items inside the multi teaser block are managed through the page form. '
				.'To add and remove items close this popup and go to the "'.$plural_name
				.'" tab. If you don\'t see this tab then you may need to Save the page first.'
		));
		$fields->addFieldsToTab('Root.Main', $field = new OptionsetField('Layout', 'Layout'));
		$field->setSource(array(
			'TwoGridCells' => 'Normal - 2 grid cells wide, one small image and text',
			'ThreeGridCells' => '3 grid cells wide, one small image and wide text',
			'FourGridCells' => '4 grid cells wide, one square image, text and an arrow',
			'FourGridCellsTwoColumn' => '2 columns of one small image and text',
		));
		$fields->addFieldToTab('Root.Advanced', $field = new NumericField('NumItemsPerPage', 'Number of items to display per page'));
		return $fields;
	}

	public function allowCreate() {
		return false;
	}

	/**
	 * @param DataObjectSet $items
	 */
	public function setItems( DataObjectSet $items ) {
		$this->items = $items;
		unset($this->cachedItems);
	}

	/**
	 * @return MultiTeaserBlockItem
	 */
	public function Items() {
		$itemRelation = $this->stat('item_relation');
		$set = ($this->items ? $this->items : $this->Page()->$itemRelation()); /* @var $set DataObjectSet */
		$set->setPageLimits((int) @$_GET['start'], 10, 10);
		if( $this->Page()->$itemRelation('OpenInLightbox = 1', null, null, 1) ) {
			MediaPage_Controller::add_lightbox_requirements();
		}
		return $set;
	}

	public function RowSpan() {
		$items = $this->Items();
		// workaround for an issue with LazyLoadComponentSet->Count()
		if( method_exists($items, 'reset') ) $items->reset();
		if( ($this->Layout == 'FourGridCells') || preg_match('/itemRowSpan1/', $this->extraCSSClasses) ) {
			$rv = $items->Count();
		}
		else {
			$rv = ceil($items->Count() / 2);
		}
		return $rv + $this->addRowSpan;
	}

	public function ColSpan() {
		if( $this->ColSpan ) {
			$rv = $this->ColSpan;
		}
		else if( $this->Layout == 'ThreeGridCells' ) {
			$rv = 3;
		}
		else if( in_array($this->Layout, array('FourGridCells', 'FourGridCellsTwoColumn')) ) {
			$rv = 4;
		}
		else {
			$rv = 2;
		}
		return $rv;
	}

	/**
	 * Returns the header or false if it has not been set.
	 * @return false|string
	 * @author Alex Hayes <alex.hayes@dimension27.com>
	 */
	public function Header() {
		return $this->header;
	}

	/**
	 * Set the header text for the widget.
	 *
	 * @param string $header
	 * @return void
	 * @author Alex Hayes <alex.hayes@dimension27.com>
	 */
	public function setHeader($header) {
		$this->header = $header;
	}

	public function CSSClasses() {
		$rv = parent::CSSClasses().' '.$this->Layout;
		return $rv;
	}

	public function Widget() {
		return $this->renderWith(get_class($this), array(
			'Layout' => $this->Layout,
			'WidgetCSSClasses' => $this->CSSClasses()
		));
	}

}

class MultiTeaserBlockItem extends DataObject {

	static $db = array(
		'Title' => 'Varchar',
		'Body' => 'HTMLText',
		'LinkLabel' => 'Varchar',
		'LinkType' => 'Enum("Internal, External, File")',
		'LinkTargetURL' => 'Varchar(255)',
		'Lightbox' => 'Boolean',
		'OpenInLightbox' => 'Boolean'
	);

	static $has_one = array(
		'LinkTarget' => 'SiteTree',
		'LinkFile' => 'File',
		'Page' => 'Page',
	);

	static $limit_words = null;

	/**
	 * @return FieldSet
	 */
	function getCMSFields() {
		$fields = new FieldSet();
		$fields->push(new TabSet("Root", $mainTab = new Tab("Main")));
		$mainTab->setTitle(_t('SiteTree.TABMAIN', "Main"));
		$fields->addFieldToTab('Root.Main', $field = new TextField('Title'));
		$fields->addFieldToTab('Root.Main', $field = new SimpleTinyMCEField('Body'));
		PageWidget::add_link_fields($fields);
		return $fields;
	}

	public function LinkURL() {
		return PageWidget::get_link_url($this);
	}
	public function LinkClass() {
		return PageWidget::get_link_class($this);
	}
	public function LinkLabel() {
		return PageWidget::get_link_label($this);
	}
	public function LinkSuffix() {
		return PageWidget::get_link_suffix($this);
	}
	public function LinkWindowTarget() {
		return PageWidget::get_link_target($this);
	}

	function Body( $limit = true ) {
		$rv = $this->getField('Body');
		if( $limit && self::$limit_words ) {
			$obj = new Text();
			$obj->setValue($rv);
			$rv = $obj->LimitWordCount(self::$limit_words);
		}
		return $rv;
	}

}

