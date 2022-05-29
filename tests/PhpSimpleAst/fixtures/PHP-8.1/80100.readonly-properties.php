<?php

class BlogData
{
	public readonly Status $status;

	public function __construct(Status $status)
	{
		$this->status = $status;
	}
}
