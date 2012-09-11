<?php

class PageWidgetDecorator extends SiteTreeDecorator {

	public function extraStatics() {
		return array(
			'db' => array(
				'ContentWidgetBody' => 'HTMLText'
			),
			'has_many' => array(
				'Widgets' => 'PageWidget',
				'LinkListItems' => 'LinkListItem',
				'MultiTeaserBlockItems' => 'MultiTeaserBlockItem',
				'MultiTeaserImageBlockItems' => 'MultiTeaserImageBlockItem'
			),
			'many_many' => array(
				'LibraryWidgets' => 'PageWidget',
			)
		);
	}

	public function updateCMSFields( FieldSet $fields ) {
		$widgets = new DataObjectManager(
			$controller = $this->owner,
			$name = 'Widgets',
			$sourceClass = 'PageWidget',
			$fieldList = array(
				'Title' => 'Title',
				'Type' => 'Type',
				'Row' => 'Row',
				'Column' => 'Column',
			),
			$detailFormFields = null,
			$sourceFilter = '',
			$sourceSort = null // this doesn't work - have to use default_sort static
		);
		$fields->addFieldToTab('Root.Content.Widgets', $widgets);
		
		$widgetLibrary = new ManyManyDataObjectManager(
			$controller = $this->owner,
			$name = 'LibraryWidgets',
			$sourceClass = 'PageWidget',
			$fieldList,
			$detailFormFields = null,
			$sourceFilter = 'IncludeInLibrary = 1',
			$sourceSort = null // this doesn't work - have to use default_sort static
		);
		$widgetLibrary->setPermissions(array('edit', 'delete'));
		$widgetLibrary->setPluralTitle('Widget Library');
		$fields->addFieldToTab('Root.Content.Widgets', $widgetLibrary);
		
		// add tabs for editing the multi-item widgets
		// LinkListItems
		$this->handleTabForWidgetItems($fields, 'LinkListWidget', 'LinkListItems', 'LinkListItem', array(
				'LinkLabel' => 'Link Label'
		));
		// MultiTeaserBlockItems
		$this->handleTabForWidgetItems($fields, 'MultiTeaserBlockWidget', 'MultiTeaserBlockItems', 
			'MultiTeaserBlockItem', array('Title' => 'Title'), true
		);
		// MultiTeaserImageBlockItems
		$this->handleTabForWidgetItems($fields, 'MultiTeaserImageBlockWidget', 'MultiTeaserImageBlockItems', 
			'MultiTeaserImageBlockItem'
		);
		// add a Content Widget tab for editing the ContentWidget content
		$widget = $this->owner->Widgets("`ClassName` = 'ContentWidget'");
		if( $widget->count() ) {
			$fields->addFieldToTab("Root.Content.ContentWidget", new HtmlEditorField('ContentWidgetBody', 'Content Widget Body'));
		}
	}

	function handleTabForWidgetItems( $fields, $widgetClassName, $relationName, $itemClass, $fieldList = null, $excludeSubClasses = null ) {
		$classes = array($widgetClassName);
		if( $excludeSubClasses !== true ) {
			$classes += ClassInfo::subclassesFor($widgetClassName);
		}
		else {
			$excludeSubClasses = null;
		}
		$widgets = $this->owner->Widgets(
			"`ClassName` IN ('".implode("', '", $classes)."')"
			.($excludeSubClasses ? " AND `ClassName` NOT IN ('".implode("', '", $excludeSubClasses)."')" : '')
		);
		if( $widgets->count() ) {
			$items = new DataObjectManager(
				$controller = $this->owner,
				$name = $relationName,
				$sourceClass = $itemClass,
				$fieldList
			);
			// $items->setParentIdName("$itemClass.PageID");
			$fields->addFieldToTab("Root.Content.$relationName", $items);
		}
	}

	function onAfterWrite() {
		$this->flushCache();
		$owner = $this->owner;
		$libraryWidgets = $owner->LibraryWidgets();
		// check to see if there's any new library widgets to be added
		foreach( $libraryWidgets as $libraryWidget ) {
			/* WORKAROUND: A non-library widget may be incorrectly added to Page_LibraryWidgets 
			 * when it is edited and then the Page is then saved. Believe this may be a problem with
			 * DataObjectManager. However, it does clean this erroneous record up - but not until 
			 * after this method is run.
			 */
			if( !$libraryWidget->IncludeInLibrary ) {
				$libraryWidgets->remove($libraryWidget);
			}
			// If this widget hasn't been added to this page
			else if( $owner->Widgets('LibraryWidgetID = '.$libraryWidget->ID)->count() == 0 ) {
				// add it to the page
				$widget = $libraryWidget->duplicate(false); /* @var $widget PageWidget */
				$widget->PageID = $owner->ID;
				$widget->LibraryWidgetID = $libraryWidget->ID;
				$widget->IncludeInLibrary = false;
				$widget->Identifer = null;
				$widget->write();
			}
		}
	}

}

?>