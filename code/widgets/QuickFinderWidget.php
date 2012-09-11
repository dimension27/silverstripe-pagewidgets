<?php
/**
 * Displays a search input and a dropdown list of links.
 */
class QuickFinderWidget extends LinkListWidget {

	static $db = array(
		'SearchLabel' => 'Varchar',
		'LinksLabel' => 'Varchar',
	);

	static $defaults = array(
		'SearchLabel' => 'Search',
		'LinksLabel' => 'Quick Links',
	);

	static $singular_name = 'Quick Finder Widget';

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Main', $field = new TextField('SearchLabel', 'Label for the search input'));
		$fields->addFieldToTab('Root.Main', $field = new TextField('LinksLabel', 'Label above the links'));
		return $fields;
	}

	function SearchForm() {
		// relies on the SearchDecorator in the search module having been added to PageController
		$form = Controller::curr()->SearchForm();
		$action = $form->Actions()->First();
		$action->setTitle('» Go');
		$action->addExtraClass('button');
		return $form;
	}

	function Widget() {
		Requirements::customScript(
"var QuickFinderWidget = {
	goToLink: function( select ) {
		var option = select.options[select.selectedIndex];
		if( option && option.value ) {
			document.location = option.value;
		}
	}
}", 'QuickFinderWidget'
		);
		return parent::Widget();
	}

}
