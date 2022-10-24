<?php

class Nonce {
	
	/**
	 * Generate salt
	 *
	 * @param $length
	 *
	 * @return string
	 */
	private function generateSalt( $length = 10 ) {
		//set up random characters
		$chars = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
		//get the length of the random characters
		$char_len = strlen( $chars ) - 1;
		//store output
		$output = '';
		//iterate over $chars
		while ( strlen( $output ) < $length ) {
			/* get random characters and append to output till the length of the output
			 is greater than the length provided */
			$output .= $chars[ rand( 0, $char_len ) ];
		}
		
		//return the result
		return $output;
	}
	
	/**
	 * Store Nonce
	 *
	 * @param $form_id
	 * @param $nonce
	 *
	 * @return bool
	 */
	private function storeNonce( $form_id, $nonce ) {
		//Argument must be a string
		if ( is_string( $form_id ) == false ) {
			throw new InvalidArgumentException( "A valid Form ID is required" );
		}
		//group Generated Nonces and store with md5 Hash
		$_SESSION[ 'nonce' ][ $form_id ] = md5( $nonce );
		
		return true;
	}
	
	/**
	 * Hash tokens and return nonce
	 *
	 * @param $length
	 * @param $form_id
	 * @param $expiry_time
	 *
	 * @return string
	 */
	public function generateNonce( $length = 10, $form_id, $expiry_time ) {
		//our secret
		$secret = NONCE_SECRET;
		
		//secret must be valid. You can add your regExp here
		if ( is_string( $secret ) == false || strlen( $secret ) < 10 ) {
			throw new InvalidArgumentException( "A valid Nonce Secret is required" );
		}
		//generate our salt
		$salt = self::generateSalt( $length );
		//convert the time to seconds
		$time = time() + ( 60 * intval( $expiry_time ) );
		//concatenate tokens to hash
		$toHash = $secret . $salt . $time;
		//send this to the user with the hashed tokens
		$nonce = $salt . ':' . $form_id . ':' . $time . ':' . hash( 'sha256', $toHash );
		//store Nonce
		self::storeNonce( $form_id, $nonce );
		
		//return nonce
		return $nonce;
	}
	
	/**
	 * Verify Nonce Cookie
	 *
	 * @param $nonce
	 *
	 * @return bool
	 */
	public function verifyNonce( $nonce ) {
		//our secret
		$secret = NONCE_SECRET;
		//split the nonce using our delimeter : and check if the count equals 4
		$split = explode( ':', $nonce );
		if ( count( $split ) !== 4 ) {
			return;
		}
		
		//reassign variables
		$salt    = $split[ 0 ];
		$form_id = $split[ 1 ];
		$time    = intval( $split[ 2 ] );
		$oldHash = $split[ 3 ];
		//check if the time has expired
		if ( time() > $time ) {
			return;
		}
		/* Nonce is proving to be valid, continue ... */
		
		//check if nonce is present in the session
		if ( isset( $_SESSION[ 'nonce' ][ $form_id ] ) ) {
			//check if hashed value matches
			if ( $_SESSION[ 'nonce' ][ $form_id ] !== md5( $nonce ) ) {
				return;
			}
		} else {
			return;
		}
		
		//check if the nonce is valid by rehashing and matching it with the $oldHash
		$toHash   = $secret . $salt . $time;
		$reHashed = hash( 'sha256', $toHash );
		//match with the token
		if ( $reHashed !== $oldHash ) {
			return;
		}
		
		/* Wonderful, Nonce has proven to be valid*/
		
		return true;
	}
}
