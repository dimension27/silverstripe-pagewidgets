<?php
/**
 * Base class that Represents all PageWidgets.
 *
 * This has a direct relationship to 'Page' which is  fairly safe assumption
 * that all silverstripe projects have.
 *
 * Otherwise the Page relationship should be removed, and added via a decorator
 * in the users CMS.
 */
class PageWidget extends DataObject {

	public static function get_by_identifier( $identifier ) {
		if( $widget = DataObject::get('PageWidget', "Identifier = '".addslashes($identifier)."'") ) {
			return $widget->pop();
		}
	}

	static $db = array (
		'Title' => 'Varchar',
		'Row' => 'Int',
		'Column' => 'Int',
		'Class' => 'Varchar',
		'RawImage' => 'Boolean',
		'CSSClass' => 'Varchar',
		'IncludeInLibrary' => 'Boolean',
		'Identifier' => 'Varchar'
	);

	static $indexes = array (
		'Identifier' => 'unique (Identifier)',
	);

	static $has_one = array(
		'Page' => 'Page',
		'LibraryWidget' => 'PageWidget'
	);

	static $extensions = array(
		// "Versioned('Stage', 'Live')",
	);

	static $singular_name = 'Widget';
	static $default_sort = '"Row" ASC, "Column" ASC';
	static $is_grid_widget = true;
	static $allow_create = true;
	public $extraCSSClasses = '';

	public function getCMSFields() {
		$fields = new FieldSet();
		$creating = (get_class($this) == 'PageWidget');
		$fields->push(new TabSet("Root", $mainTab = new Tab("Main")));
		$mainTab->setTitle(_t('SiteTree.TABMAIN', "Main"));
		if( $creating ) {
			global $_ALL_CLASSES;
			$options = array();
			foreach( $_ALL_CLASSES['children'][$this->class] as $klass ) {
				$widget = new $klass(); /* @var $widget PageWidget */
				if( $widget->allowCreate() ) {
					$options[$klass] = $widget->singular_name();
				}
			}
			ksort($options);
			$fields->addFieldToTab('Root.Main', new DropdownField('ClassName', 'Widget Type', $options));
		}
		$fields->addFieldToTab('Root.Main', new TextField('Title'));
		$fields->addFieldToTab('Root.Main', new TextField('Row', 'Row Number'));
		$fields->addFieldToTab('Root.Main', new TextField('Column', 'Column Number')); /* @var $field FormField */
		$fields->addFieldToTab('Root.Advanced', new TextField('CSSClass', 'Extra CSS class for this widget'));
		$fields->addFieldToTab('Root.Advanced', new CheckboxField('RawImage', 'Don\'t resize images for this widget'));
		if( !$this->IncludeInLibrary ) {
			$fields->addFieldToTab('Root.Advanced', new CheckboxField('IncludeInLibrary', 'Add this widget to the Widget Library (creates a duplicate of this in the library)'));
		}
		return $fields;
	}

	static public function getByIdentifier($identifier) {
		return DataObject::get_one('PageWidget', "Identifier = '$identifier'");
	}

	public function validate() {
		$result = new ValidationResult();
		if( self::$is_grid_widget ) {
			if( !is_numeric($this->Row) || ($this->Row <= 0) ) {
				$result->error('Please enter a Row greater than 0');
			}
			if( !is_numeric($this->Column) || $this->Column <= 0 || $this->Column > 4 ) {
				$result->error('Please enter a Column between 1 and 4');
			}
		}
		return $result;
	}

	public function getValidator() {
		$validator = new CustomRequiredFields(array('ClassName', 'Row', 'Column'));
		return $validator;
	}

	public function getType() {
		return $this->singular_name();
	}

	public function Widget() {
		return $this->renderWith(get_class($this));
	}

	public function CSSClasses() {
		$classes = get_class($this);
		if( $this->CSSClass ) {
			$classes .= ' '.$this->CSSClass;
		}
		if( ($rowSpan = $this->RowSpan()) > 1 ) {
			$classes .= ' rowSpan'.$rowSpan;
		}
		if( $colSpan = $this->ColSpan() ) {
			$classes .= ' colSpan'.$colSpan;
		}
		if( $this->extraCSSClasses ) {
			$classes .= ' ' . $this->extraCSSClasses;
		}
		return $classes;
	}

	public function RowSpan() {
		return ($this->RowSpan ? $this->RowSpan : 1);
	}

	public function ColSpan() {
		return ($this->ColSpan ? $this->ColSpan : 1);
	}

	public function LinkClass() {
		return PageWidget::get_link_class($this);
	}

	public static function get_link_class( $widget ) {
		return (@$widget->LinkClass ? $widget->LinkClass.' ' : '')
				.strtolower(substr($widget->LinkType, 0, 1)).substr($widget->LinkType, 1)
				.($widget->OpenInLightbox ? ' lightbox' : '');
	}

	public function LinkWindowTarget() {
		return PageWidget::get_link_target($this);
	}

	public static function get_link_target( $widget ) {
		return in_array($widget->LinkType, array('External', 'File')) ? '_blank' : '';
	}

	static function set_upload_folder( FileField $field, $dataObject = null, $subDir = null ) {
		UploadFolderManager::setUploadFolder('PageWidget', $field, $subDir);
	}

	public function LinkURL() {
		return PageWidget::get_link_url($this);
	}

	public function SizedImage( $width, $height ) {
		if( ($image = $this->Image) || $image = $this->Image() ) {
			if( $this->RawImage ) {
				return $image;
			}
			else {
				return $image->CroppedImage($width, $height);
			}
		}
	}

	public static function add_link_fields( $fields, $tab = 'Root.Link' ) {
		$fields->addFieldToTab($tab, $field = new TextField('LinkLabel', 'Link label'));
		$group = new SelectionGroup('LinkType', array(
				'Internal//Link to a page on this website' => new TreeDropdownField('LinkTargetID', 'Link target', 'SiteTree'),
				'External//Link to an external website' => new TextField('LinkTargetURL', 'Link target URL'),
				'File//Download a file' => new TreeDropdownField('LinkFileID', 'Download file', 'File')
		));
		$fields->addFieldToTab($tab, $group);
		$fields->addFieldToTab($tab, new CheckboxField('OpenInLightbox', 'Open the link in a lightbox'));
	}

	public static function get_link_url( $widget ) {
		switch( $widget->LinkType ) {
			case 'External':
				return $widget->LinkTargetURL;
			case 'Internal':
				if( ($target = $widget->LinkTarget()) && $target->exists() ) {
					return $target->Link();
				}
				else {
					return $widget->LinkTargetURL;
				}
				break;
			case 'File':
				if( $target = $widget->LinkFile() ) {
					return $target->Link();
				}
				break;
		}
	}

	public function LinkLabel() {
		return self::get_link_label($this);
	}

	public static function get_link_label( $widget ) {
		return $widget->getField('LinkLabel');
	}

	public function LinkSuffix() {
		return self::get_link_suffix($this);
	}

	public static function get_link_suffix( $widget ) {
		switch( $widget->LinkType ) {
			case 'File':
				$target = $widget->LinkFile(); /* @var $target File */
				$path = $target->getFullPath();
				if( $target->exists() && is_file($path) ) {
					$info = pathinfo($path);
					$extension = strtolower($info['extension']);
					return " <span class='suffix'><span class='file $extension'>$extension, "
								.self::format_file_size(filesize($path)).'</span></span>';
				}
		}
	}

	public static function format_file_size( $size ) {
		if ($size == 0) {
			return 'n/a';
		}
		else {
			$sizes = array(' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
			return (round($size/pow(1024, ($i = floor(log($size, 1024)))), $i > 1 ? 1 : 0) . $sizes[$i]);
		}
	}

	public function onAfterWrite() {
		// if this should be in the library
		if( $this->IncludeInLibrary
				// and is associated with a page
				&& $this->PageID ) {
			// then create the library widget as a clone of this
			$libraryWidget = $this->duplicate(false);
			// remove the association with a page (this will avoid recursion too)
			$libraryWidget->record['PageID'] = 0;
			$libraryWidget->PageID = 0;
			$libraryWidget->write();
			// store the association with the library widget
			$this->LibraryWidgetID = $libraryWidget->ID;
			// unset the flag so that this widget doesn't get included in the library
			$this->IncludeInLibrary = 0;
			$this->write();
		}
		parent::onAfterWrite();
	}

	public function onAfterDelete() {
		// if this widget was created from the library
		if( $this->LibraryWidgetID ) {
			// remove the association
			$widget = DataObject::get_by_id(get_class($this), $this->LibraryWidgetID);
			$this->Page()->LibraryWidgets()->remove($widget);
		}
	}

	public function onBeforeDelete() {
		// If we have an Identifier, then we can't be deleted.
		// Not Certain how best to prevent this, the examples
		// all just 'exit()'. Would be better to fail validation?
		if( $this->Identifier ) {
			throw new Exception('Cannot delete a PageWidget that has an Identifier - these are reserved for internal use');
		}
		return parent::onBeforeDelete();
	}

	public function isGridWidget() {
		return $this->stat('is_grid_widget');
	}

	public function isContentWidget() {
		return !$this->stat('is_grid_widget');
	}

	public function allowCreate() {
		return true;
	}

	public function addCSSClass( $class ) {
		$this->extraCSSClasses .= ($this->extraCSSClasses ? ' ' : '').$class;
	}

	public function __toString() {
		return '['.get_class($this).": $this->Title, RowSpan: {$this->RowSpan()}, ColSpan: {$this->ColSpan()}]";
	}

	function duplicate($doWrite = true) {
		$myRecord = $this->record;
		$this->record['Identifier'] = null;
		$rv = parent::duplicate($doWrite);
		$this->record = $myRecord;
		return $rv;
	}

}

UploadFolderManager::setOptions('PageWidget', array(
	'folder' => 'widgets',
	'subsite' => true
));
