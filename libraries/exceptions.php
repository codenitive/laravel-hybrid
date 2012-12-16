<?php namespace Hybrid;

/**
 * Exceptions
 *
 * @package    Hybrid
 * @author     Laravel Hybrid Development Team
 */

class Exception extends \Exception {}
class InvalidArgumentException extends Exception {}
class OutOfBoundsException extends Exception {}
class RuntimeException extends Exception {}
class AclException extends Exception {}