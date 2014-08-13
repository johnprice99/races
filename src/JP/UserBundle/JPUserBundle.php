<?php

namespace JP\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class JPUserBundle extends Bundle {

	public function getParent() {
		return 'FOSUserBundle';
	}
}
