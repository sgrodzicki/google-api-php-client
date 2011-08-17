<?php
/*
 * Copyright 2011 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
session_start();

require_once '../../src/apiClient.php';
require_once '../../src/contrib/apiUrlshortenerService.php';

global $apiConfig;

// Visit https://code.google.com/apis/console to
// generate your client id, client secret, and redirect uri.
// $apiConfig['oauth2_client_id'] = 'insert_your_oauth2_client_id';
// $apiConfig['oauth2_client_secret'] ='insert_your_oauth2_client_secret';
// $apiConfig['oauth2_redirect_uri'] = 'insert_your_oauth2_redirect_uri';
$client = new apiClient();
$service = new apiUrlshortenerService($client);

if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_GET['code'])) {
  $client->authenticate();
  $_SESSION['access_token'] = $client->getAccessToken();
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
} else {
  $authUrl = $client->createAuthUrl();
}

if ($client->getAccessToken() && isset($_POST['url'])) {
  // Start to make API requests.
  //$location = $service->location->listLocation();
  $url = new Url();
  $url->longUrl = $_POST['url'];
  $short = $service->url->insert($url);
  $_SESSION['access_token'] = $client->getAccessToken();
}
?>
<!doctype html>
<html>
<head><link rel='stylesheet' href='style.css' /></head>
<body>
<header><h1>Google Url Shortener Sample App</h1></header>
<div class="box">
  <div class="request">
    <?php if (isset($authUrl)): ?>
      <a class='login' href='<?php print $authUrl; ?>'>Connect Me!</a>
    <?php else: ?>
      <form id="url" method="GET" action="index.php">
        <input name="url" class="url" type="text">
        <input type="submit" value="Shorten">
      </form>
      <a class='logout' href='?logout'>Logout</a>
    <?php endif ?>
  </div>

  <?php if (isset($short)): ?>
    <div class="shortened">
      <pre><?php var_dump($short); ?></pre>
    </div>
  <?php endif ?>
</div>
</body></html>