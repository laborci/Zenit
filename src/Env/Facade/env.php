<?php use Zenit\Core\Env\Component\Env;

function env($key = null){ return Env::Service()->get($key); }
function setenv($key, $value){ Env::Service()->set($key, $value); }
