<?php declare(strict_types=1);

/*
 * This file is part of the Digital Foundation packages.
 *
 * (c) Bagrat Hakobyan <b.a.hakobyan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DigitalFoundation\UtilBundle\BusinessLogic\Infrastructure;


/**
 * Class Util
 *
 * @author Bagrat Hakobyan <b.a.hakobyan@gmail.com>
 *
 * @package DigitalFoundation\UtilBundle\BusinessLogic\Infrastructure
 */
class Util {

    /**
     * @param array  $assocArray
     * @param string $field
     * @param int    $direction
     *
     * @return array
     */
    static public function sortByCustomField(array $assocArray, string $field, int $direction = SORT_ASC): array {
        $fields = [];
        $aggregatedArray = [];
        foreach ($assocArray as $item) {
            $fields[] = $item[$field];
            $aggregatedArray[$item[$field]][] = $item;
        }

        $fields = array_unique($fields);

        $sortFunctionName = ($direction == SORT_ASC) ? "sort" : "rsort";
        $sortFunctionName($fields);

        $result = [];
        foreach ($fields as $field) {
            foreach ($aggregatedArray[$field] as $item) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    static public function camelCaseToSpace(string $string): string {
        $string = self::camelCaseToUnderscore($string);

        $words = explode("_", $string);
        foreach ($words as &$word) {
            $word = ucfirst($word);
        }

        return implode(" ", $words);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    static public function camelCaseToUnderscore(string $string): string {
        return strtolower(ltrim(preg_replace('/[A-Z]/', '_$0', $string), '_'));
    }

	/**
	 * @param string $string
     *
	 * @return string
	 */
	static public function underscoreToCamelCase(string $string): string {
		return lcfirst(str_replace('_', '', ucwords($string, '_')));
	}
}