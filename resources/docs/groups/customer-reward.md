# Customer Reward

Endpoints for customer related to reward redemption procedures.

## Get code used to redeem a rewards with a link (e.g. QR code).

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/campaign/get-redeem-reward-token?uuid=283ca865-a71c-4d4a-b8cb-8c46c5b3ca57&reward=703fcac9-9ffa-4527-9b3d-c9549e02a353" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/get-redeem-reward-token"
);

let params = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "reward": "703fcac9-9ffa-4527-9b3d-c9549e02a353",
};
Object.keys(params)
    .forEach(key => url.searchParams.append(key, params[key]));

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://loyalty.web/api/campaign/get-redeem-reward-token',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'query' => [
            'uuid'=> '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'reward'=> '703fcac9-9ffa-4527-9b3d-c9549e02a353',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-campaign-get-redeem-reward-token" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-campaign-get-redeem-reward-token"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-campaign-get-redeem-reward-token"></code></pre>
</div>
<div id="execution-error-POSTapi-campaign-get-redeem-reward-token" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-campaign-get-redeem-reward-token"></code></pre>
</div>
<form id="form-POSTapi-campaign-get-redeem-reward-token" data-method="POST" data-path="api/campaign/get-redeem-reward-token" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-campaign-get-redeem-reward-token', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-campaign-get-redeem-reward-token" onclick="tryItOut('POSTapi-campaign-get-redeem-reward-token');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-campaign-get-redeem-reward-token" onclick="cancelTryOut('POSTapi-campaign-get-redeem-reward-token');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-campaign-get-redeem-reward-token" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/campaign/get-redeem-reward-token</code></b>
</p>
<p>
<label id="auth-POSTapi-campaign-get-redeem-reward-token" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-campaign-get-redeem-reward-token" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-campaign-get-redeem-reward-token" data-component="query" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>reward</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="reward" data-endpoint="POSTapi-campaign-get-redeem-reward-token" data-component="query" required  hidden>
<br>
uuid of reward.
</p>
</form>


## Merchant verifies generated code

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/campaign/reward/verify-merchant-code" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","code":"deleniti"}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/reward/verify-merchant-code"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "code": "deleniti"
}

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response => response.json());
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://loyalty.web/api/campaign/reward/verify-merchant-code',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'code' => 'deleniti',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-campaign-reward-verify-merchant-code" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-campaign-reward-verify-merchant-code"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-campaign-reward-verify-merchant-code"></code></pre>
</div>
<div id="execution-error-POSTapi-campaign-reward-verify-merchant-code" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-campaign-reward-verify-merchant-code"></code></pre>
</div>
<form id="form-POSTapi-campaign-reward-verify-merchant-code" data-method="POST" data-path="api/campaign/reward/verify-merchant-code" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-campaign-reward-verify-merchant-code', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-campaign-reward-verify-merchant-code" onclick="tryItOut('POSTapi-campaign-reward-verify-merchant-code');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-campaign-reward-verify-merchant-code" onclick="cancelTryOut('POSTapi-campaign-reward-verify-merchant-code');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-campaign-reward-verify-merchant-code" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/campaign/reward/verify-merchant-code</code></b>
</p>
<p>
<label id="auth-POSTapi-campaign-reward-verify-merchant-code" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-campaign-reward-verify-merchant-code" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-campaign-reward-verify-merchant-code" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>code</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="code" data-endpoint="POSTapi-campaign-reward-verify-merchant-code" data-component="body" required  hidden>
<br>
Code.
</p>

</form>


## Initially merhant code was correct, double check code and process reward and segments

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/campaign/reward/process-merchant-entry" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","code":"ratione","reward":"703fcac9-9ffa-4527-9b3d-c9549e02a353","segments":"doloremque"}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/reward/process-merchant-entry"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "code": "ratione",
    "reward": "703fcac9-9ffa-4527-9b3d-c9549e02a353",
    "segments": "doloremque"
}

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response => response.json());
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://loyalty.web/api/campaign/reward/process-merchant-entry',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'code' => 'ratione',
            'reward' => '703fcac9-9ffa-4527-9b3d-c9549e02a353',
            'segments' => 'doloremque',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-campaign-reward-process-merchant-entry" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-campaign-reward-process-merchant-entry"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-campaign-reward-process-merchant-entry"></code></pre>
</div>
<div id="execution-error-POSTapi-campaign-reward-process-merchant-entry" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-campaign-reward-process-merchant-entry"></code></pre>
</div>
<form id="form-POSTapi-campaign-reward-process-merchant-entry" data-method="POST" data-path="api/campaign/reward/process-merchant-entry" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-campaign-reward-process-merchant-entry', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-campaign-reward-process-merchant-entry" onclick="tryItOut('POSTapi-campaign-reward-process-merchant-entry');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-campaign-reward-process-merchant-entry" onclick="cancelTryOut('POSTapi-campaign-reward-process-merchant-entry');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-campaign-reward-process-merchant-entry" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/campaign/reward/process-merchant-entry</code></b>
</p>
<p>
<label id="auth-POSTapi-campaign-reward-process-merchant-entry" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-campaign-reward-process-merchant-entry" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-campaign-reward-process-merchant-entry" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>code</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="code" data-endpoint="POSTapi-campaign-reward-process-merchant-entry" data-component="body" required  hidden>
<br>
Code.
</p>
<p>
<b><code>reward</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="reward" data-endpoint="POSTapi-campaign-reward-process-merchant-entry" data-component="body" required  hidden>
<br>
uuid of reward.
</p>
<p>
<b><code>segments</code></b>&nbsp;&nbsp;<small>integer[].</small>     <i>optional</i> &nbsp;
<input type="text" name="segments" data-endpoint="POSTapi-campaign-reward-process-merchant-entry" data-component="body"  hidden>
<br>
id of segments Example : [1, 2]
</p>

</form>



