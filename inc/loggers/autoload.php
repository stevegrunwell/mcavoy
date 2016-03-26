<?php
/**
 * Not-quite-autoloading for core loggers.
 *
 * This file simply acts as an intermediary loader so *all* loggers are automatically available.
 *
 * @package McAvoy
 * @author  Steve Grunwell
 */

namespace McAvoy\Loggers;

// Load the base Logger class.
require_once __DIR__ . '/class-logger.php';

// Load the various Loggers.
require_once __DIR__ . '/database.php';
