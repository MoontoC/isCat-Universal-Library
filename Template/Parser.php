<?php

namespace isCat\Template;

class Parser
{
	const TAG_BEGIN  = '<';
	const TAG_END    = '>';
	const TAG_CLOSE  = '/';
	const TAG_PREFIX = 'T\:';

	const INLINE_TAG_BEGIN = '\{';
	const INLINE_TAG_END   = '\}';

	static protected $config = array();

	protected $source;
	protected $output;

	protected $offset = 0;
	protected $template;

	public function __construct( $templateContent )
	{
		$this->source = $templateContent;
	}

	public function splitSource()
	{
		return preg_split(
							'#' .
								'(' . self::TAG_BEGIN . self::TAG_CLOSE . '?' . self::TAG_PREFIX . '[a-z]+(?:\s+[a-z]+=\".*?\")*\s*' . self::TAG_CLOSE . '?' . self::TAG_END . ')' .
								'|' .
								'(' . self::INLINE_TAG_BEGIN . '\$?[a-z\d]+(?:\s+[a-z]+=\".*?\")*' . self::INLINE_TAG_END . ')' .
							'#i',

							$this->source,
							-1,
							PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE
				);
	}

	public function parser()
	{
		$TAG_BEGIN  = str_replace( '\\', '', self::TAG_BEGIN  );
		$TAG_END    = str_replace( '\\', '', self::TAG_END    );
		$TAG_CLOSE  = str_replace( '\\', '', self::TAG_CLOSE  );
		$TAG_PREFIX = str_replace( '\\', '', self::TAG_PREFIX );

		$INLINE_TAG_BEGIN = str_replace( '\\', '', self::INLINE_TAG_BEGIN );
		$INLINE_TAG_END   = str_replace( '\\', '', self::INLINE_TAG_END   );

		while ( list($IN, $this->offset) = array_shift($this->source) )
		{
			// <TAG>
			if ( $TAG_BEGIN == $IN[0] AND $TAG_END == $IN[strlen($IN) - 1]
				 AND preg_match(
									'#^' . self::TAG_BEGIN . '(?P<ENDTag>' . self::TAG_CLOSE . ')?' . self::TAG_PREFIX .
										'(?P<TagName>[a-z]+)(?P<Params>\s+[a-z]+=\".*\")*\s*(?P<SingleTag>' . self::TAG_CLOSE . ')?' . self::TAG_END . '$#i',
									$IN, $matches) )
			{
				$isENDTag    = ! empty($matches['ENDTag']);
				$isSingleTag = ! empty($matches['SINGLETag']);
				$isBEGINTag  = ( ! $isSingleTag && ! $isENDTag );
				$tag         = \strtolower( $matches['TagName'] );
				$params      = ( ! $isENDTag && ! empty( $matches['Params'] ) ) ? self::parseTagParams( $matches['Params'] ) : array();

				var_dump(array($tag, $isENDTag, $isSingleTag, $params));
			}

			// {VAR}
			elseif ( $INLINE_TAG_BEGIN == $IN[0] AND $INLINE_TAG_END == $IN[strlen($IN) - 1] )
			{

			}

			// TEXT
			else
			{

			}
		}
	}

	public function parseTagParams( $param )
	{
		$params = array();

		if ( preg_match_all('#(?P<key>[a-z]+)=\"(?P<value>.*?)\"#i', $param, $matches, PREG_SET_ORDER) )
			foreach ( $matches as $param )
				$params[$param['key']] = $param['value'];
		else
			return array();

		return $params;
	}

	public function reader()
	{
		$this->source = $this->splitSource();

		$this->parser();

		#$this->out = '<' . "?php\n\n" . $this->code . "\n\n// EOF";

// 		\var_dump($this->code);
// 		\var_dump($this->output);
	}
}