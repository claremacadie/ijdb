<?php
//This file creates the interface 'Routes'
//This describes the methods that a class should contain
//This enables type hinting to ensure that classes are input with the correct type of methods 

namespace Ninja;

interface Routes
{
	public function getRoutes();
}