<?php

class Samurai_Error extends Exception
{
	public function __construct($message, $name) {
		parent::__construct($message);
		$this->name = $name;
	}
}


# 400
class Samurai_BadRequestError extends Samurai_Error
{
	public function __construct() {
		parent::__construct('Bad Request', 'badRequestError');
	}
}

# 401
class Samurai_AuthenticationRequiredError extends Samurai_Error
{
	public function __construct() {
		parent::__construct('Authentication Required Error', 'authenticationRequiredError');
	}
}

# 403
class Samurai_AuthorizationError extends Samurai_Error
{
	public function __construct() {
		parent::__construct('Authorization Error', 'authorizationError');
	}
}

# 404
class Samurai_NotFoundError extends Samurai_Error
{
	public function __construct() {
		parent::__construct('Not Found', 'notFoundError');
	}
}

# 406
class Samurai_NotAcceptable extends Samurai_Error
{
	public function __construct() {
		parent::__construct('Not Acceptable', 'notAcceptableError');
	}
}

# 500
class Samurai_InternalServerError extends Samurai_Error
{
	public function __construct() {
		parent::__construct('Internal Server Error', 'internalServerError');
	}
}

# 503
class Samurai_DownForMaintenanceError extends Samurai_Error
{
	public function __construct() {
		parent::__construct('Down for Maintenance', 'downForMaintenanceError');
	}
}

# Everything else
class Samurai_UnexpectedError extends Samurai_Error
{
	public function __construct($message) {
		parent::__construct($message, 'unexpectedError');
	}
}
