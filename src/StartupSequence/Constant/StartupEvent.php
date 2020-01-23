<?php namespace Zenit\Core\StartupSequence\Constant;

interface StartupEvent{
	const MODULES_LOADED = __CLASS__.'MODULES_LOADED';
	const DONE = __CLASS__.'DONE';
}