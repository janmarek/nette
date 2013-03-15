<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Nette\Templating;

use Nette;
use Nette\Latte\Engine;

/**
 * @author Jan Marek
 */
class LatteFactory extends Nette\Object implements ILatteFactory
{

	public function createLatte()
	{
		return new Engine();
	}

}
