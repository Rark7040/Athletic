<?php
declare(strict_types=1);

namespace rarkhopper\athletic\player;

class PlayerAthleticAttribute{
	public bool $allowAthleticAction = true;
	public bool $isJumping = false;
	public bool $isDoubleJumped = false;
	public bool $isBlockJumping = false;
	public bool $isBlockJumped = false;
	public bool $isOnGround = true;
	public bool $isSliding = false;
	public bool $keepSliding = false;
}
