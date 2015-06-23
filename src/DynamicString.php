<?php
/**
 * DynamicString.php
 * ----------------------------------------------
 *
 *
 * @author      Stanislav Kiryukhin <korsar.zn@gmail.com>
 * @copyright   Copyright (c) 2015, CKGroup.ru
 *
 * ----------------------------------------------
 * All Rights Reserved.
 * ----------------------------------------------
 */
namespace sKGroup\Std\String;

/**
 * Class DynamicString
 * @package sKGroup\Std\String
 */
class DynamicString
{
	const PLACEHOLDER_PREFIX = '___PLACEHOLDER_';

	/**
	 * @var string
	 */
	protected $leftDelimiter = '{';

	/**
	 * @var string
	 */
	protected $rightDelimiter = '}';

	/**
	 * @var string
	 */
	protected $wordsSeparator = '|';

	/**
	 * @var string
	 */
	protected $string;

	/**
	 * @var string
	 */
	protected $stringPrepared;

	/**
	 * @var array
	 */
	protected $placeholders = [];

	/**
	 * @param $str
	 */
	public function __construct($str)
	{
		$this->setString($str);
	}

	/**
	 * @return string
	 */
	public function getString()
	{
		return $this->string;
	}

	/**
	 * @param string $string
	 */
	public function setString($string)
	{
		$this->string = $string;
		$this->prepareString();
	}

	/**
	 * @return string
	 */
	public function getLeftDelimiter()
	{
		return $this->leftDelimiter;
	}

	/**
	 * @param string $leftDelimiter
	 */
	public function setLeftDelimiter($leftDelimiter)
	{
		$this->leftDelimiter = $leftDelimiter;
	}

	/**
	 * @return string
	 */
	public function getRightDelimiter()
	{
		return $this->rightDelimiter;
	}

	/**
	 * @param string $rightDelimiter
	 */
	public function setRightDelimiter($rightDelimiter)
	{
		$this->rightDelimiter = $rightDelimiter;
	}

	/**
	 * @return string
	 */
	public function getWordsSeparator()
	{
		return $this->wordsSeparator;
	}

	/**
	 * @param string $wordsSeparator
	 */
	public function setWordsSeparator($wordsSeparator)
	{
		$this->wordsSeparator = $wordsSeparator;
	}

	/**
	 * @return string
	 */
	public function generate()
	{
		$string = $this->stringPrepared;

		for ($i = count($this->placeholders) - 1; $i >= 0; $i--) {

			$key  = $this->placeholders[$i]['key'];
			$word = $this->getRandomWord($i);

			$string = str_replace($key, $word, $string);
		}

		return $string;
	}

	/**
	 * @param null $string
	 * @return bool
	 */
	public function checkSyntax($string = null)
	{
		$str = $string !== null ? $string : $this->getString();
		return substr_count($str, $this->getLeftDelimiter()) === substr_count($str, $this->getRightDelimiter());
	}

	/**
	 * @param $placeholderIndex
	 * @return string
	 */
	protected function getRandomWord($placeholderIndex)
	{
		$words = $this->placeholders[$placeholderIndex]['words'];
		return $words[array_rand($words)];
	}

	/**
	 *
	 */
	protected function prepareString()
	{
		if (!$this->checkSyntax($this->string)) {
			throw new \RuntimeException('Syntax error in string "' . $this->string . '"');
		}

		$ld_s = preg_quote($this->getLeftDelimiter());
		$rd_s = preg_quote($this->getRightDelimiter());

		$this->placeholders = [];
		$placeholderNumber  = 0;

		$pattern = '/' . $ld_s . '([^' . $ld_s . $rd_s . ']+)' . $rd_s . '/';
		$string  = $this->string;

		while (strpos($string, $this->leftDelimiter) !== false) {
			$string = preg_replace_callback($pattern, function($matches) use (&$placeholderNumber) {

				$key = static::PLACEHOLDER_PREFIX . ($placeholderNumber++);
				$this->placeholders[] = [
					'key' => $key,
					'words' => explode($this->wordsSeparator, $matches[1])
				];

				return $key;

			}, $string);
		}

		$this->stringPrepared = $string;
	}
}
