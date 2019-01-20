<?php

/**
 * @file
 *
 * Contains convenience wrappers around Cloudflare API V4 calls.
 */

/**
 * Purges the provided domains from the provided zone.
 *
 * Note - this function returns immediately and does not wait for the list
 * of domains to finish purging.  Cloudflare seems to not support querying
 * on the process status.
 *
 * If the request fails for any reason, this script terminates immediately.
 *
 * @param string $auth_email
 *   The authorization email to initiate the API request with.
 * @param string $auth_key
 *   The authorization key to initiate the API request with.
 * @param string $zone_id
 *   The Zone ID to access.
 * @param array $domains
 *   The list of domains to purge.
 *
 * @return array
 *   An array containing the JSON response from Cloudflare.
 */
function cfapi_purge_domains($auth_email, $auth_key, $zone_id, $domains) {

  $response = NULL;
  $error = TRUE;

  if ($ch = curl_init()) {

    $options = [
      CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/{$zone_id}/purge_cache",
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_POSTFIELDS => json_encode(['hosts' => $domains]),
      CURLOPT_POST => 1,
      CURLOPT_HTTPHEADER => [
        "X-Auth-Email: {$auth_email}",
        "X-Auth-Key: {$auth_key}",
        "Content-Type: application/json"
      ]
    ];

    if (curl_setopt_array($ch, $options)) {
      $response = curl_exec($ch);
      $error = curl_errno($ch);
    }
    curl_close($ch);
  }

  if($error) {
    // If absolutely anything went wrong, there's no recovering from this.
    exit(1);
  }

  $response = json_decode($response, TRUE);

  if(!isset($response['success']) || !strcasecmp($response['success'], 'true')) {
    exit(1);
  }
  return $response;
}
