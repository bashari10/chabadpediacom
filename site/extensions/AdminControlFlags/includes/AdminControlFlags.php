<?php

use MediaWiki\MediaWikiServices;

class AdminControlFlags {

    public static function getFlags() {
        $services = MediaWikiServices::getInstance();
        $mainConfig = $services->getMainConfig();

        return [
            'blockAnonEdit' => $mainConfig->get('BlockAnonEdit') ?? false,
            'blockUserCreate' => $mainConfig->get('BlockUserCreate') ?? false,
        ];
    }

    // Hook: BeforePageDisplay
    public static function onBeforePageDisplay( $out, $skin ): void {
        $user = $out->getUser();
        if ( $user->isAllowed( 'sysop' ) ) {
            $out->addHTML('<div style="background:#ff9;padding:5px;">AdminControlFlags is active ✅</div>');

            // Add a small toggle link for sysops (this is just an example)
            $out->addHTML( '<div style="background:#eee;padding:5px;">[Admin Controls Placeholder]</div>' );
        }
    }

    // Hook: UserCan (controls edit permission)
    public static function onUserCan( $title, $user, $action, &$result ) {
        $flags = self::getFlags();
        if ( $flags['blockAnonEdit'] && !$user->isRegistered() && $action === 'edit' ) {
            $result = false;
            return false;
        }
        return true;
    }

    // Hook: AbortNewAccount (controls new account creation)
    public static function onAbortNewAccount( $user, &$abortError ) {
        $flags = self::getFlags();
        if ( $flags['blockUserCreate'] ) {
           $abortError = wfMessage( 'usercreationblocked', 'User creation is temporarily disabled by admin.' )->text();
            return false;
        }
        return true;
    }
}
