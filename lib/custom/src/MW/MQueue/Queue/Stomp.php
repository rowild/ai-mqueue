<?php

namespace Aimeos\MW\MQueue\Queue;


class Stomp implements Iface
{
	private $client;
	private $queue;


	/**
	 * Initializes the message queue class
	 *
	 * @param \Stomp\Stomp $client Stomp object
	 * @param string $queue Message queue name
	 * @throws \Aimeos\MW\MQueue\Exception
	 */
	public function __construct( \Stomp\Stomp $client, string $queue )
	{
		if( $client->subscribe( $queue ) === false ) {
			throw new \Aimeos\MW\MQueue\Exception( sprintf( 'Unable to subscribe to queue "%1$s"', $queue ) );
		}

		$this->client = $client;
		$this->queue = $queue;
	}


	/**
	 * Unsubscribes from the queue on cleanup
	 */
	public function __destruct()
	{
		$this->client->unsubscribe( $this->queue );
	}


	/**
	 * Adds a new message to the message queue
	 *
	 * @param string $msg Message, e.g. JSON encoded data
	 * @return \Aimeos\MW\MQueue\Queue\Iface MQueue queue instance for method chaining
	 * @throws \Aimeos\MW\MQueue\Exception
	 */
	public function add( string $msg ) : \Aimeos\MW\MQueue\Queue\Iface
	{
		if( $this->client->send( $this->queue, $msg ) === false )
		{
			$msg = sprintf( 'Sending message to queue "%1$s" failed: ' . $msg, $this->queue );
			throw new \Aimeos\MW\MQueue\Exception( $msg );
		}

		return $this;
	}


	/**
	 * Removes the message from the queue
	 *
	 * @param \Aimeos\MW\MQueue\Message\Iface $msg Message object
	 * @return \Aimeos\MW\MQueue\Iface MQueue instance for method chaining
	 * @throws \Aimeos\MW\MQueue\Exception
	 */
	public function del( \Aimeos\MW\MQueue\Message\Iface $msg ) : \Aimeos\MW\MQueue\Queue\Iface
	{
		if( $this->client->ack( $msg->getObject() ) === false ) {
			throw new \Aimeos\MW\MQueue\Exception( 'Couldn\'t acknowledge frame: ' . $msg->getBody() );
		}

		return $this;
	}


	/**
	 * Returns the next message from the queue
	 *
	 * @return \Aimeos\MW\MQueue\Message\Iface|null Message object or null if none is available
	 */
	public function get() : ?\Aimeos\MW\MQueue\Message\Iface
	{
		try
		{
			if( $this->client->hasFrameToRead() && ( $msg = $this->client->readFrame() ) !== false ) {
				return new \Aimeos\MW\MQueue\Message\Stomp( $msg );
			}
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\MW\MQueue\Exception( $e->getMessage() );
		}

		return null;
	}
}
