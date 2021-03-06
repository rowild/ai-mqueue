<?php

namespace Aimeos\MW\MQueue\Message;


class AMQP implements Iface
{
	private $msg;


	/**
	 * Initializes the message object
	 *
	 * @param \PhpAmqpLib\Message\AMQPMessage $msg AMQPMessage object
	 */
	public function __construct( \PhpAmqpLib\Message\AMQPMessage $msg )
	{
		$this->msg = $msg;
	}


	/**
	 * Returns the message body
	 *
	 * @return string Message body
	 */
	public function getBody() : string
	{
		return $this->msg->body;
	}


	/**
	 * Returns the original message object
	 *
	 * @return \PhpAmqpLib\Message\AMQPMessage AMQPMessage object
	 */
	public function getObject() : \PhpAmqpLib\Message\AMQPMessage
	{
		return $this->msg;
	}
}
