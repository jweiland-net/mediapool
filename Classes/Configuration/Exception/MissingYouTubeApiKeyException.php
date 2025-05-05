<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/glossary2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Configuration\Exception;

/**
 * This exception will be thrown if you/admin/someone has forgotten to set the YouTube API key in extension settings
 */
class MissingYouTubeApiKeyException extends \InvalidArgumentException {}
