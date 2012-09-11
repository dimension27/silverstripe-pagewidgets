<?php

class NewsWidget extends PageWidget {

	static $db = array(
		'Title' => 'Varchar',
		'Heading' => 'Varchar',
		'NumItemsToDisplay' => 'Int',
		'LinkLabel' => 'Varchar',
		'LinkType' => 'Enum("Internal, External, File")',
		'LinkTargetURL' => 'Varchar(255)',
		'OpenInLightbox' => 'Boolean',
		'RowSpan' => 'Int',
	);

	static $defaults = array(
		'NumItemsToDisplay' => '4'
	);

	static $has_one = array(
		'NewsHolder' => 'NewsHolder',
		'LinkTarget' => 'SiteTree',
		'LinkFile' => 'File'
	);

	static $singular_name = 'News Widget';

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$page = $this->Page();
		if( ($page->exists() && (get_class($page) == 'NewsHolder'))
				|| (($page = $page->Parent()) && $page->exists() && (get_class($page) == 'NewsHolder')) ) {
			if( !$this->NewsHolderID ) {
				$this->NewsHolderID = $page->ID;
			}
			if( !$this->LinkTargetID ) {
				$this->LinkTargetID = $page->ID;
			}
		}
		$fields->addFieldToTab('Root.Main', $field = new TextField('Heading', 'Heading'), 'Row');
		$fields->addFieldToTab('Root.Main', $field = new TreeDropdownField(
			'NewsHolderID', 'News Holder to use for the list of news items', 'SiteTree'), 'Row'
		);
		$fields->addFieldToTab('Root.Main', $field = new NumericField('NumItemsToDisplay', 'Number of news items to display'), 'Row');
		PageWidget::add_link_fields($fields);
		$fields->addFieldToTab('Root.Advanced', $field = new NumericField('RowSpan', 'Number of grid rows to span'));
		return $fields;
	}

	function FeaturedNewsItems( $numResults = null ) {
		if( !$numResults ) {
			$numResults = $this->NumItemsToDisplay;
		}
		if( !$numResults ) {
			$numResults = 5;
		}
		return NewsItem::getFeaturedNewsItems($this->NewsHolder(), $numResults);
	}

	function RowSpan() {
		if( !$rows = $this->RowSpan ) {
			$rows = 1;
			if( $items = $this->FeaturedNewsItems() ) {
				$numItems = $items->count();
				if( $numItems > 1 ) {
					$rows = 1 + ceil(($numItems - 1) / 2);
				}
			}
		}
		return $rows;
	}

}
