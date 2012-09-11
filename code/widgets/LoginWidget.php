<?php
/**
 * Login Block
 * Display a login dialog. The URL to direct to afterwards is configurable.
 *
 * The Body is display if the user is logged in.
 *
 */
class LoginWidget extends ContentWidget {

	static $db = array(
		'LandingPageURL' => 'Varchar(255)',
		'ShowRegisterLink' => 'Boolean',
	);

	static $has_one = array(
		'LandingPage' => 'SiteTree',
	);

	static $singular_name = 'Login Widget';
	static $content_note = 'The content that is displayed when the user is logged-in can be edited using the "Content Widget" tab of the Page';
	
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Main', new TreeDropdownField('LandingPageID', 'Landing Page', 'SiteTree'));
		$fields->addFieldToTab('Root.Advanced', new TextField('LandingPageURL', 'Landing Page URL (this overrides the Landing Page)'));
		$fields->addFieldToTab('Root.Advanced', new CheckboxField('ShowRegisterLink', 'Show "Register" link'));
		return $fields;
	}

	public function BackURL() {
		return ($this->LandingPageURL ? htmlentities($this->LandingPageURL) : 
				($this->LandingPage()->exists() ? $this->LandingPage()->Link() : ''
		));
	}

	public function validate() {
		return PageWidget::validate();
	}

	public function isLoggedIn() {
		return Member::currentUserID();
	}

	public function Member() {
		return Member::currentUser();
	}

	public function LoggedInContent() {
		$this->bodyViewerData['Member'] = $this->Member();
		return $this->Body();
	}

}
