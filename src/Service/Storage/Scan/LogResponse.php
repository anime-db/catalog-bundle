<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service\Storage\Scan;

use Symfony\Component\HttpFoundation\JsonResponse;

class LogResponse extends JsonResponse
{
    // add option JSON_UNESCAPED_UNICODE
    // 271 === JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
    protected $encodingOptions = 271;

    /**
     * @param string $log
     * @param int $offset
     * @param bool $is_end
     *
     * @return self
     */
    public static function logOffset($log, $offset = 0, $is_end = false)
    {
        if (is_numeric($offset) && $offset > 0) {
            $offset_log = (string) mb_substr($log, $offset, mb_strlen($log, 'UTF-8') - $offset, 'UTF-8');

            try {
                return new self([
                    'offset' => $offset,
                    'content' => $offset_log,
                    'end' => $is_end,
                ]);
            } catch (\Exception $e) {
                // use default condition
            }
        }

        return new self([
            'offset' => 0,
            'content' => $log,
            'end' => $is_end,
        ]);
    }
}
