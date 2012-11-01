<?php
namespace isCat\Strings;

abstract class Strings
{

	/**
	 * 返回输入内容的字符内容
	 * <p>* 字符串值,数字内容会转换为字符后返回<br>* 有toString 或 __toString方法的对象也可直接返回字串<br>*
	 * toString 方法优先, 方法必须公开且无输入参数需求</p>
	 *
	 * @param mixed input
	 * @return string
	 */
	static public function getText ($input)
	{
		if ( is_string($input) || is_integer($input) || is_float($input) )
		{
			return (string) $input;
		}
		elseif ( is_object($input) )
		{
			$ref = new \ReflectionObject($input);

			if ( $ref->hasMethod('toString') &&
					 $ref->getMethod('toString')->isPublic() &&
					 0 === $ref->getMethod('toString')->getNumberOfParameters() )
				return $input->toString();
			elseif ( $ref->hasMethod('__toString') &&
					 $ref->getMethod('toString')->isPublic() )
				return (string) $input;
		}

		// None
		return '';
	}

	/**
	 * 生成随机字串
	 * <p>固定生成字符范围 0-9A-Za-z</p>
	 *
	 * @param int length
	 * @return string
	 */
	static function randomHash ($length = 8)
	{
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$max = strlen($characters) - 1;

		$length = ($length < 2 || $length > 256) ? 8 : (int) $length;

		mt_srand(crc32(microtime() . uniqid(mt_rand(), TRUE)));

		$hash = '';
		while ( $length-- )
		{
			$hash .= $characters[mt_rand(0, $max)];
		}

		mt_srand();

		return $hash;
	}

	/**
	 * 计算文本字符个数, 而非字节数(不包括\r\n\t\f等)
	 */
	static function wordCount( $text )
	{
		// FIXME 需要处理空格, 需要循环应付大文本
		preg_match_all("/./us", $text, $text);
		return sizeof( $text[0] );
	}
}