<?php

namespace MediaWiki\Extension\BoilerPlate;

use MediaWiki\MediaWikiServices;
use MediaWiki\Output\OutputPage;
use MediaWiki\Skin\Skin;

class Hooks implements \MediaWiki\Hook\BeforePageDisplayHook {

	public function onBeforePageDisplay( $out, $skin ): void {
		$user = $out->getUser();
		$title = $out->getTitle();
		$request = $out->getRequest();

		// 1️⃣ BLOCK ACCOUNT CREATION
		$disableRegistration = strtolower( trim( $this->getInterfaceMessage( 'DisableRegistration' ) ) );
		if ( $disableRegistration === 'true' || $disableRegistration === 'yes' ) {
			if ( $title->isSpecial( 'CreateAccount' ) ) {
				$out->clearHTML();
				$block_message = trim( $this->getInterfaceMessage( 'DisableAccountMessage' ) );
				$out->addHTML( '<div class="errorbox">'.$block_message.'</div>' );
				$out->setPageTitle( 'Account creation disabled' );
				$out->addReturnTo($title);
				return;
			}
		}

		// 2️⃣ BLOCK ANONYMOUS EDITS
		$blockAnonEdits = strtolower( trim( $this->getInterfaceMessage( 'BlockAnonEdits' ) ) );
		if ( ( $blockAnonEdits === 'true' || $blockAnonEdits === 'yes' ) && $user->isAnon() ) {

			$action = $request->getVal( 'action', 'view' );
			if ( $action === 'edit' || $title->isSpecial( 'EditPage' ) ) {
				$out->clearHTML();
				$out->addHTML( '<div class="errorbox">Anonymous editing is currently disabled. Please log in to edit pages.</div>' );
				$out->setPageTitle( 'Editing disabled for anonymous users' );
				$out->addReturnTo($title);
				return;
			}
		}
	}

	private function getInterfaceMessage( $messageKey ) {
		$services = MediaWikiServices::getInstance();
		$title = $services->getTitleFactory()->newFromText( 'MediaWiki:' . $messageKey );
		$page = $services->getWikiPageFactory()->newFromTitle( $title );
		$content = $page->getContent();
		return $content ? $content->getNativeData() : '';
	}
}
