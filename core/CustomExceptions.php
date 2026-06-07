<?php

/**
 * Excepciones personalizadas del proyecto
 * Heredan de clases base de PHP para mejor manejo
 */

class ValidationException extends \DomainException {}
class NotFoundException extends \DomainException {}
class UnauthorizedException extends \Exception {}
class ConflictException extends \DomainException {}
class InternalServerException extends \Exception {}
