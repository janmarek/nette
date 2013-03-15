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

use Nette\Application\UI\Control;

/**
 * @author Jan Marek
 */
interface ITemplateFactory
{
	/**
	 * @param \Nette\Application\UI\Control $control
	 * @return \Nette\Templating\FileTemplate
	 */
	public function createTemplate(Control $control = NULL);

}
