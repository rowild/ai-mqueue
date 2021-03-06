<?php

namespace Aimeos\MW\MQueue;


class StompTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp() : void
	{
		if( class_exists( '\Stomp\Stomp' ) === false ) {
			$this->markTestSkipped( 'Please install the "stomp-php" composer package first' );
		}
	}


	public function testProcess()
	{
		try
		{
			$config = array( 'username' => 'guest', 'password' => 'guest' );
			$mqueue = new \Aimeos\MW\MQueue\Stomp( $config );
			$queue = $mqueue->getQueue( 'aimeos_unittest' );
		}
		catch( \Aimeos\MW\MQueue\Exception $e )
		{
			$this->markTestSkipped( 'No Stomp compliant server available at "localhost"' );
		}

		$queue->add( 'testmsg' );
		$msg = $queue->get();
		$queue->del( $msg );

		$this->assertNull( $queue->get() );
	}


	public function testGetQueueException()
	{
		$object = new \Aimeos\MW\MQueue\Stomp( array( 'host' => 'tcp://127.0.0.1:61616' ) );

		$this->expectException( \Aimeos\MW\MQueue\Exception::class );
		$object->getQueue( 'test' );
	}


	public function testGetQueue()
	{
		$client = $this->getMockBuilder( \Stomp\Stomp::class )
			->disableOriginalConstructor()
			->getMock();

		$object = $this->getMockBuilder( \Aimeos\MW\MQueue\Stomp::class )
			->setMethods( array( 'connect' ) )
			->disableOriginalConstructor()
			->getMock();

		$object->expects( $this->once() )->method( 'connect' )
			->will( $this->returnValue( $client ) );

		$this->assertInstanceOf( \Aimeos\MW\MQueue\Queue\Iface::class, $object->getQueue( 'test' ) );
	}
}
