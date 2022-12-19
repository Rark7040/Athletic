<?php
declare(strict_types=1);

namespace rarkhopper\athletic\player;

class PlayerAthleticAttribute{
	/** @var bool if this attribute is set to false, actions cannot be performed. */
	public bool $allowAthleticAction = true;
	
	/** @var bool this toggle remains true until the player performs a double jump after jumping */
	public bool $isJumping = false;
	
	/** @var bool this toggle remains true until the player performs a fall on ground after double jumping */
	public bool $isDoubleJumped = false;
	
	/** @var bool this toggle remains true until the player performs a block jump after jumping */
	public bool $isBlockJumping = false;
	
	/** @var bool this toggle remains true until the player performs a fall on ground after block jumping */
	public bool $isBlockJumped = false;
	
	/**
	 * @var bool
	 * this toggle works in conjunction with {@link Player::isOnGround()}
	 * to determine whether the player was in the air during the previous tick.
	 *
	 * {@link UpdateOnGroundAttributeTrait::updateOnGroundAttr()}
	 */
	public bool $isOnGround = true;
	
	/**
	 * @var bool
	 * this toggle becomes true when the player is sliding.
	 * if also in the case of the sliding is keeping, toggle to false.
	 */
	public bool $isSliding = false;
	
	/** @var bool when sliding ends, this toggle becomes true if there is a solid block above the player's top */
	public bool $keepSliding = false;
}
