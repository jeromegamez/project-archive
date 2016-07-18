<?php

if ( !isset( $argv[1] ) )
{
    exit( 'Extension name not given' );
}

require_once 'autoload.php';

$extensionName = $argv[1];

$siteIni = eZINI::instance( 'site.ini', null, null, false );

$activeExtensions = $siteIni->variable( 'ExtensionSettings', 'ActiveExtensions' );
$activeExtensions = array_unique( array_merge( $activeExtensions, array( $extensionName ) ) );

$siteIni->setVariable( 'ExtensionSettings', 'ActiveExtensions', $activeExtensions );
$siteIni->save( false, '.append.php', true, true );

eZExtension::activateExtensions();
