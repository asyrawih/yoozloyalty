# Customer Point

Endpoints related to redeeming points for customer

## Get customer points.

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/campaign/points?uuid=283ca865-a71c-4d4a-b8cb-8c46c5b3ca57" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/points"
);

let params = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
};
Object.keys(params)
    .forEach(key => url.searchParams.append(key, params[key]));

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response => response.json());
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://loyalty.web/api/campaign/points',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'query' => [
            'uuid'=> '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


> Example response (200):

```json
{
    "error": "Unauthorized"
}
```
<div id="execution-results-GETapi-campaign-points" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-campaign-points"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-campaign-points"></code></pre>
</div>
<div id="execution-error-GETapi-campaign-points" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-campaign-points"></code></pre>
</div>
<form id="form-GETapi-campaign-points" data-method="GET" data-path="api/campaign/points" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-campaign-points', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-campaign-points" onclick="tryItOut('GETapi-campaign-points');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-campaign-points" onclick="cancelTryOut('GETapi-campaign-points');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-campaign-points" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/campaign/points</code></b>
</p>
<p>
<label id="auth-GETapi-campaign-points" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-campaign-points" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="GETapi-campaign-points" data-component="query" required  hidden>
<br>
uuid of website.
</p>
</form>


## Get code used to claim points with a link (e.g. QR code).

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/campaign/get-claim-points-token" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57"}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/get-claim-points-token"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57"
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
    'https://loyalty.web/api/campaign/get-claim-points-token',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-campaign-get-claim-points-token" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-campaign-get-claim-points-token"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-campaign-get-claim-points-token"></code></pre>
</div>
<div id="execution-error-POSTapi-campaign-get-claim-points-token" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-campaign-get-claim-points-token"></code></pre>
</div>
<form id="form-POSTapi-campaign-get-claim-points-token" data-method="POST" data-path="api/campaign/get-claim-points-token" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-campaign-get-claim-points-token', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-campaign-get-claim-points-token" onclick="tryItOut('POSTapi-campaign-get-claim-points-token');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-campaign-get-claim-points-token" onclick="cancelTryOut('POSTapi-campaign-get-claim-points-token');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-campaign-get-claim-points-token" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/campaign/get-claim-points-token</code></b>
</p>
<p>
<label id="auth-POSTapi-campaign-get-claim-points-token" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-campaign-get-claim-points-token" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-campaign-get-claim-points-token" data-component="body" required  hidden>
<br>
uuid of website.
</p>

</form>


## Customer verifies code generated by merchant

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/campaign/earn/verify-customer-code" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","code":"deleniti"}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/earn/verify-customer-code"
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
    'https://loyalty.web/api/campaign/earn/verify-customer-code',
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


<div id="execution-results-POSTapi-campaign-earn-verify-customer-code" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-campaign-earn-verify-customer-code"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-campaign-earn-verify-customer-code"></code></pre>
</div>
<div id="execution-error-POSTapi-campaign-earn-verify-customer-code" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-campaign-earn-verify-customer-code"></code></pre>
</div>
<form id="form-POSTapi-campaign-earn-verify-customer-code" data-method="POST" data-path="api/campaign/earn/verify-customer-code" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-campaign-earn-verify-customer-code', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-campaign-earn-verify-customer-code" onclick="tryItOut('POSTapi-campaign-earn-verify-customer-code');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-campaign-earn-verify-customer-code" onclick="cancelTryOut('POSTapi-campaign-earn-verify-customer-code');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-campaign-earn-verify-customer-code" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/campaign/earn/verify-customer-code</code></b>
</p>
<p>
<label id="auth-POSTapi-campaign-earn-verify-customer-code" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-campaign-earn-verify-customer-code" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-campaign-earn-verify-customer-code" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>code</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="code" data-endpoint="POSTapi-campaign-earn-verify-customer-code" data-component="body" required  hidden>
<br>
Code.
</p>

</form>


## Merchant verifies generated code

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/campaign/earn/verify-merchant-code" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","code":"laudantium"}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/earn/verify-merchant-code"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "code": "laudantium"
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
    'https://loyalty.web/api/campaign/earn/verify-merchant-code',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'code' => 'laudantium',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-campaign-earn-verify-merchant-code" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-campaign-earn-verify-merchant-code"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-campaign-earn-verify-merchant-code"></code></pre>
</div>
<div id="execution-error-POSTapi-campaign-earn-verify-merchant-code" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-campaign-earn-verify-merchant-code"></code></pre>
</div>
<form id="form-POSTapi-campaign-earn-verify-merchant-code" data-method="POST" data-path="api/campaign/earn/verify-merchant-code" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-campaign-earn-verify-merchant-code', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-campaign-earn-verify-merchant-code" onclick="tryItOut('POSTapi-campaign-earn-verify-merchant-code');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-campaign-earn-verify-merchant-code" onclick="cancelTryOut('POSTapi-campaign-earn-verify-merchant-code');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-campaign-earn-verify-merchant-code" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/campaign/earn/verify-merchant-code</code></b>
</p>
<p>
<label id="auth-POSTapi-campaign-earn-verify-merchant-code" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-campaign-earn-verify-merchant-code" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-campaign-earn-verify-merchant-code" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>code</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="code" data-endpoint="POSTapi-campaign-earn-verify-merchant-code" data-component="body" required  hidden>
<br>
Code.
</p>

</form>


## Initially merhant code was correct, double check code and process points and segments

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/campaign/earn/process-merchant-entry" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","code":"doloremque","points":7,"segments":"ipsam"}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/earn/process-merchant-entry"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "code": "doloremque",
    "points": 7,
    "segments": "ipsam"
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
    'https://loyalty.web/api/campaign/earn/process-merchant-entry',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'code' => 'doloremque',
            'points' => 7,
            'segments' => 'ipsam',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-campaign-earn-process-merchant-entry" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-campaign-earn-process-merchant-entry"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-campaign-earn-process-merchant-entry"></code></pre>
</div>
<div id="execution-error-POSTapi-campaign-earn-process-merchant-entry" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-campaign-earn-process-merchant-entry"></code></pre>
</div>
<form id="form-POSTapi-campaign-earn-process-merchant-entry" data-method="POST" data-path="api/campaign/earn/process-merchant-entry" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-campaign-earn-process-merchant-entry', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-campaign-earn-process-merchant-entry" onclick="tryItOut('POSTapi-campaign-earn-process-merchant-entry');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-campaign-earn-process-merchant-entry" onclick="cancelTryOut('POSTapi-campaign-earn-process-merchant-entry');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-campaign-earn-process-merchant-entry" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/campaign/earn/process-merchant-entry</code></b>
</p>
<p>
<label id="auth-POSTapi-campaign-earn-process-merchant-entry" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-campaign-earn-process-merchant-entry" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-campaign-earn-process-merchant-entry" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>code</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="code" data-endpoint="POSTapi-campaign-earn-process-merchant-entry" data-component="body" required  hidden>
<br>
Code.
</p>
<p>
<b><code>points</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
<input type="number" name="points" data-endpoint="POSTapi-campaign-earn-process-merchant-entry" data-component="body" required  hidden>
<br>
points.
</p>
<p>
<b><code>segments</code></b>&nbsp;&nbsp;<small>integer[].</small>     <i>optional</i> &nbsp;
<input type="text" name="segments" data-endpoint="POSTapi-campaign-earn-process-merchant-entry" data-component="body"  hidden>
<br>
id of segments Example : [1, 2]
</p>

</form>



