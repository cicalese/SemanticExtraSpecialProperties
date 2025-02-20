<?php

namespace SESP\Tests\PropertyAnnotators;

use MediaWiki\MediaWikiServices;
use SESP\PropertyAnnotators\NamespacePropertyAnnotator;
use SMWDIString as DIString;
use SMW\DIProperty;
use SMW\DIWikiPage;
use User;

/**
 * @covers \SESP\PropertyAnnotators\NamespacePropertyAnnotator
 * @group semantic-extra-special-properties
 *
 * @license GNU GPL v2+
 * @since 2.0
 *
 * @author mwjames
 */
class NamespacePropertyAnnotatorTest extends \PHPUnit_Framework_TestCase {

	private $property;
	private $appFactory;

	protected function setUp(): void {
		parent::setUp();

		$this->appFactory = $this->getMockBuilder( '\SESP\AppFactory' )
			->disableOriginalConstructor()
			->getMock();

		$this->property = new DIProperty( '___NAMESPACE' );
	}

	public function testCanConstruct() {

		$this->assertInstanceOf(
			NamespacePropertyAnnotator::class,
			new NamespacePropertyAnnotator( $this->appFactory )
		);
	}

	public function testIsAnnotatorFor() {

		$annotator = new NamespacePropertyAnnotator(
			$this->appFactory
		);

		$this->assertTrue(
			$annotator->isAnnotatorFor( $this->property )
		);
	}

	public function testAddAnnotation() {
		$user = User::newFromName( "UnitTest" );
		$userNS = MediaWikiServices::getInstance()
				->getNamespaceInfo()->getCanonicalName( $user->getUserPage()->getNamespace() );

		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'addPropertyObjectValue' )
			->with(
				$this->equalTo( $this->property ),
				$this->equalTo( new DIString( $userNS ) ) );
		$annotator = new NamespacePropertyAnnotator(
			$this->appFactory
		);

		$annotator->setNamespace( $userNS );

		$annotator->addAnnotation( $this->property, $semanticData );
	}

	public function testRemoval() {
		$semanticData = $this->getMockBuilder( '\SMW\SemanticData' )
			->disableOriginalConstructor()
			->getMock();

		$semanticData->expects( $this->once() )
			->method( 'removeProperty' )
			->with( $this->equalTo( $this->property ) );

		$annotator = new NamespacePropertyAnnotator(
			$this->appFactory
		);

		$annotator->setNamespace( false );

		$annotator->addAnnotation( $this->property, $semanticData );
	}
}
