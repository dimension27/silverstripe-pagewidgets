<?php
/**
 * Content Block
 * Plain old Content.
 */
class ContentWidget extends PageWidget {

    static $db = array (
    	'RowSpan' => 'Int',
    	'ColSpan' => 'Int',
    	'BodyTemplateFile' => 'Varchar',
	);

	static $defaults = array(
		'RowSpan' => 1,
		'ColSpan' => 2,
	);

	static $singular_name = 'Content Widget';
	static $content_note = 'The content for this widget can be edited using the "Content Widget" tab of the Page';

	public $bodyViewer;
	public $bodyViewerData = array();

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Main', $field = new LiteralField('Note', $this->content_note), 'Title');
		$fields->addFieldToTab('Root.Main', $field = new NumericField('RowSpan', 'Number of rows to span'));
		$fields->addFieldToTab('Root.Main', $field = new NumericField('ColSpan', 'Number of columns to span'));
		return $fields;
	}

	public function validate() {
		$result = parent::validate();
		if( !is_numeric($this->RowSpan) || ($this->RowSpan <= 0) ) {
			$result->error('Please enter a Row Span greater than 0');
		}
		if( !is_numeric($this->ColSpan) || $this->ColSpan <= 0 || $this->ColSpan > 4 ) {
			$result->error('Please enter a Col Span between 1 and 4');
		}
		return $result;
	}

	public function setBodyContent( $content ) {
		$this->bodyContent = $content;
	}

	public function setBodyTemplate( $includeFile, $data = null ) {
		$this->bodyViewer = new SSViewer($includeFile);
		if( $data ) {
			$this->bodyViewerData = $data;
		}
	}

	public function setBodyData( $name, $value ) {
		$this->bodyViewerData[$name] = $value;
	}

	public function Body() {
		if( $this->BodyTemplateFile ) {
			$this->setBodyTemplate($this->BodyTemplateFile);
			if( !$this->bodyViewerData ) {
				$this->bodyViewerData = $this;
			}
		}
		if( $this->bodyViewer ) {
			$data = is_object($this->bodyViewerData) ? $this->bodyViewerData : new ArrayData($this->bodyViewerData);
			return $this->bodyViewer->process($data);
		}
		else if( $this->bodyContent ) {
			return $this->bodyContent;
		}
		else {
			return '<div class="bd">'.$this->Page()->ContentWidgetBody.'</div>';
		}
	}

}
