<?php

namespace TractorCow\OpenGraph;


use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use TractorCow\OpenGraph\IOGApplication;
use TractorCow\OpenGraph\OpenGraph;


class OpenGraphSiteConfigExtension extends DataExtension implements IOGApplication
{	
	public static function get_extra_config($class, $extensionClass, $args) {
		
		$db = array();
		
		if (OpenGraph::get_config('application_id') == SiteConfig::class) {
			$db['OGApplicationID'] = 'Varchar(255)';
		}
		
		if (OpenGraph::get_config('admin_id') == SiteConfig::class) {
			$db['OGAdminID'] = 'Varchar(255)';
		}
		
		return array(
			'db' => $db
		);
	}
	
	public function updateCMSFields(FieldList $fields) {
		
		if (OpenGraph::get_config('application_id') == SiteConfig::class) {
			$fields->addFieldToTab(
				'Root.Facebook', 
				new TextField('OGApplicationID', 'Facebook Application ID', null, 255)
			);
		}
		
		if (OpenGraph::get_config('admin_id') == SiteConfig::class) {
			$fields->addFieldToTab(
				'Root.Facebook',
				new TextField('OGAdminID', 'Facebook Admin ID(s)', null, 255)
			);
		}
	}
	
	protected function getConfigurableField($dbField, $configField) {
		$value = OpenGraph::get_config($configField);
		if ($value == SiteConfig::class) {
			return $this->owner->getField($dbField);
		}
		return $value;
	}

	public function getOGAdminID()
	{
		return $this->getConfigurableField('OGAdminID', 'admin_id');
	}

	public function getOGApplicationID()
	{
		return $this->getConfigurableField('OGApplicationID', 'application_id');
	}
}