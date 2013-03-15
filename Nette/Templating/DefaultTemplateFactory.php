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
use Nette\Application\UI\Control;

/**
 * @author Jan Marek
 */
class DefaultTemplateFactory extends Nette\Object implements ITemplateFactory
{
	/** @var ILatteFactory */
	private $latteFactory;

	/** @var Nette\Security\User */
	private $user;

	/** @var Nette\Http\IResponse */
	private $httpResponse;

	/** @var Nette\Http\IRequest */
	private $httpRequest;

	/** @var Nette\Caching\IStorage */
	private $cacheStorage;

	/** @var Nette\Caching\IStorage */
	private $templateCacheStorage;

	private $helperLoaders = array('Nette\Templating\Helpers::loader');

	private $helpers;



	public function __construct(Nette\Caching\IStorage $cacheStorage, Nette\Http\IRequest $httpRequest,
		Nette\Http\IResponse $httpResponse, ILatteFactory $latteFactory, Nette\Caching\IStorage $templateCacheStorage,
		Nette\Security\User $user)
	{
		$this->cacheStorage = $cacheStorage;
		$this->httpRequest = $httpRequest;
		$this->httpResponse = $httpResponse;
		$this->latteFactory = $latteFactory;
		$this->templateCacheStorage = $templateCacheStorage;
		$this->user = $user;
	}


	/**
	 * @param  Nette\Templating\Template
	 * @return void
	 */
	public function templatePrepareFilters($template)
	{
		$template->registerFilter($this->latteFactory->createLatte());
	}



	/**
	 * @param callable $helperLoader
	 */
	public function registerHelperLoader($helperLoader)
	{
		if (!is_callable($helperLoader)) {
			throw new Nette\InvalidArgumentException('Helper loader is not callable.');
		}

		$this->helperLoaders[] = $helperLoader;
	}



	/**
	 * @param string $name helper name
	 * @param callable $helper
	 */
	public function registerHelper($name, $helper)
	{
		if (!is_callable($helper)) {
			throw new Nette\InvalidArgumentException('Helper is not callable.');
		}

		$this->helpers[$name] = $helper;
	}



	/**
	 * @param \Nette\Application\UI\Control $control
	 * @return \Nette\Templating\FileTemplate
	 */
	public function createTemplate(Control $control = NULL)
	{
		$template = new Nette\Templating\FileTemplate;
		$presenter = $control->getPresenter(FALSE);
		$template->onPrepareFilters[] = $this->templatePrepareFilters;

		foreach ($this->helperLoaders as $helperLoader) {
			$template->registerHelperLoader($helperLoader);
		}

		foreach ($this->helpers as $name => $helper) {
			$template->registerHelper($name, $helper);
		}

		// default parameters
		$template->control = $template->_control = $this;
		$template->presenter = $template->_presenter = $presenter;

		if ($presenter instanceof Nette\Application\UI\Presenter) {
			$template->setCacheStorage($this->templateCacheStorage);
			$template->user = $this->user;
			$template->netteHttpResponse = $this->httpResponse;
			$template->netteCacheStorage = $this->cacheStorage;
			$template->baseUri = $template->baseUrl = rtrim($this->httpRequest->getUrl()->getBaseUrl(), '/');
			$template->basePath = preg_replace('#https?://[^/]+#A', '', $template->baseUrl);

			// flash message
			if ($presenter->hasFlashSession()) {
				$id = $control->getParameterId('flash');
				$template->flashes = $presenter->getFlashSession()->$id;
			}
		}
		if (!isset($template->flashes) || !is_array($template->flashes)) {
			$template->flashes = array();
		}

		return $template;
	}

}
