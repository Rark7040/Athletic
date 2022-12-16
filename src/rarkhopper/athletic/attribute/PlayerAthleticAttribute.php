<?php
declare(strict_types=1);

namespace rarkhopper\athletic\attribute;

class PlayerAthleticAttribute{
	public bool $canDoubleJump = true;
	public bool $isJumping = false;
	public bool $isBlockJumping = false;
	public bool $isOnGround = true;
}
