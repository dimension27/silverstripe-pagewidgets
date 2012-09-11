<?php
/**
 * List of Links
 * Has a title, and up to six links
 *
 * When a relationship is created to this class, it is import to
 * also create one_to_many to LinkListItem, as it will look for these
 * when it generates the View.
 */

class LinkListWidget extends PageWidget {
	static $db = array(
		'Title' => 'Varchar',
		'LinkSourceType' => 'Enum("OwningPage, SpecificPage")',
	);
	static $defaults = array(
		'LinkSourceType' => 'SpecificPage'
	);
	static $has_one = array(
		'LinkSourcePage' => 'Page',
	);
	static $singular_name = 'Link List Widget';
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Main', new TextField('Title'), 'Row');
		$fields->addFieldToTab('Root.Advanced', $field = new SelectionGroup('LinkSourceType', array(
			'OwningPage//The list of links comes from this page' => new LiteralField('OwningPage', ''),
			'SpecificPage//The list of links comes from another page...' => 
				new TreeDropdownField('LinkSourcePageID', 'Choose page', 'SiteTree'),
		)));
		return $fields;
	}
	public function Items() {
		return $this->getLinkSource()->LinkListItems();
	}
	public function getLinkSource() {
		if( $this->LinkSourceType == 'SpecificPage' ) {
			return $this->LinkSourcePage();
		}
		else {
			return $this->Page();
		}
	}
}

/**
 * An item in a LinkList.
 */
class LinkListItem extends DataObject {
	static $db = array(
		'LinkLabel' => 'Varchar',
		'LinkType' => 'Enum("Internal, External, File")',
		'LinkTargetURL' => 'Varchar(255)',
		'OpenInLightbox' => 'Boolean'
	);
	static $has_one = array(
		'LinkTarget' => 'SiteTree',
		'LinkFile' => 'File',
		'Page' => 'Page'
	);
	static $singular_name = 'List Item';
	function getCMSFields() {
		$fields = new FieldSet();
		$fields->push(new TabSet("Root", $mainTab = new Tab("Main")));
		$mainTab->setTitle(_t('SiteTree.TABMAIN', "Main"));
		PageWidget::add_link_fields($fields, 'Root.Main');
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
}
