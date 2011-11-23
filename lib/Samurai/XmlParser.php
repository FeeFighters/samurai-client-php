<?php

class Samurai_XmlParser 
{
	private function __construct() {}
	private function __clone() {}
	private function __wakeup() {}

	public static function parse($string) {
		$xml = simplexml_load_string($string);
		$result = array();
		$result[$xml->getName()] = self::normalize($xml);

		return $result;
	}

  private static function normalize($object, $inArray = false) {
		$result = array();

    foreach ($object->children() as $node) {
			$name = $node->getName();
			$type = $node['type'];

      switch ($type) {
        case 'boolean':
          $res = (string) $node === 'true';
          break;

        case 'datetime':
					// Our JSON deserialization doesn't include conversion of time 
					// values to native types, so skip this until we have 
					// something resembling an identity map.
					//$res = new DateTime($node);
					$res = (string) $node;
          break;

        case 'integer':
          $res = (integer) $node;
          break;

        case 'array':
          $res = self::normalize($node, true);
          break;

        default:
					if (count($node->children())) {
						$res = self::normalize($node);
					} else {
						// Deal with items that should just be an array of 
						// attributes (like most <message> items).
						if ($node->attributes()) {
							$res = array();
							foreach ($node->attributes() as $k => $v) {
								$res[$k] = (string) $v;
							}
							// Sometimes a <message> would include a text node with 
							// the optional description of the message.
							$text = (string) $node;
							if (!empty($text)) {
								$res['$t'] = $text;
							}
						} else {
							$res = (string) $node;
						}
					}
          break;
      }

			// If we're dealing with an array, create one. Note that the 
			// wrapping tags of array items will be left out. In other words, 
			// if you have a <messages><message /><message />...</messages> 
			// structure, you'll get an array('messages' => array(0 => firstItem, 
			// 1 => secondItem)) array.

			if (array_key_exists($name, $result) || $inArray) {
				$inArray = true;

				// A hacky way of figuring out whether we're already dealing 
				// with the indexed array, or still referencing the assoc array 
				// with the list of attributes.
				if (!isset($result[0]) && isset($result[$name])) {
					$result = array($result[$name]);
				}
				$result[] = $res;
			} else {
				$result[$name] = $res;
			}
    }

		return $result;
  }
}
