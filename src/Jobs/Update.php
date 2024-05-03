<?php

namespace MediaWiki\Extension\SmartComments\Jobs;

use MediaWiki\Extension\SmartComments\Updater\Page;

class Update extends \Job {

	/**
	 * @inheritDoc
	 */
	public function __construct( \Title $title, $params = [] ) {
		parent::__construct( 'SmartCommentsUpdateJob', $title, $params );
	}

	/**
	 * Removes the data slot if the page has no comments
	 *
	 * @return true
	 * @throws \MWException
	 */
	public function run() {
		if ( $this->title && $this->title->exists() ) {
			$wikiPage = \WikiPage::factory( $this->title );
			$pageUpdater = new Page( $wikiPage );
			$hasComments = $pageUpdater->hasComments();
			if ( !$pageUpdater->hasComments() ) {
				$pageUpdater->destroyDataSlot();
				$this->setMetadata( 'destroyedDataSlot', 'yes' );
			}
			$this->setMetadata('hasComments', $hasComments ? 'yes' : 'no' );
		}
		return true;
	}
}