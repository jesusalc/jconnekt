<?php

	$consumer = getConsumer();

	// Complete the authentication process using the server's
	// response.
	$return_to = getReturnTo();
	$response = $consumer->complete($return_to);

	// Check the response status.
	if ($response->status == Auth_OpenID_CANCEL) {
		// This means the authentication was cancelled.
		$msg = 'Verification cancelled.';
	} else if ($response->status == Auth_OpenID_FAILURE) {
		// Authentication failed; display the error message.
		$msg = "OpenID authentication failed: " . $response->message;
	} else if ($response->status == Auth_OpenID_SUCCESS) {
		// This means the authentication succeeded; extract the
		// identity URL and Simple Registration data (if it was
		// returned).
		$openid = $response->getDisplayIdentifier();
		$esc_identity = escape($openid);

		$success = sprintf('You have successfully verified ' .
                           '<a href="%s">%s</a> as your identity.',
		$esc_identity, $esc_identity);

		if ($response->endpoint->canonicalID) {
			$escaped_canonicalID = escape($response->endpoint->canonicalID);
			$success .= '  (XRI CanonicalID: '.$escaped_canonicalID.') ';
		}

		$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);

		$sreg = $sreg_resp->contents();

		if (@$sreg['email']) {
			$success .= "  You also returned '".escape($sreg['email']).
                "' as your email.";
		}

		if (@$sreg['nickname']) {
			$success .= "  Your nickname is '".escape($sreg['nickname']).
                "'.";
		}

		if (@$sreg['fullname']) {
			$success .= "  Your fullname is '".escape($sreg['fullname']).
                "'.";
		}

		echo $success;
	}

