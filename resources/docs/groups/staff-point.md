# Staff Point

Endpoints related to handling redemption for staff

## Validate if link token is (still) valid

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/staff/points/validate-link-token?uuid=283ca865-a71c-4d4a-b8cb-8c46c5b3ca57&token=ab" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/points/validate-link-token"
);

let params = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "token": "ab",
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
    'https://loyalty.web/api/staff/points/validate-link-token',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'query' => [
            'uuid'=> '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'token'=> 'ab',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-staff-points-validate-link-token" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-staff-points-validate-link-token"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-staff-points-validate-link-token"></code></pre>
</div>
<div id="execution-error-POSTapi-staff-points-validate-link-token" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-staff-points-validate-link-token"></code></pre>
</div>
<form id="form-POSTapi-staff-points-validate-link-token" data-method="POST" data-path="api/staff/points/validate-link-token" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-staff-points-validate-link-token', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-staff-points-validate-link-token" onclick="tryItOut('POSTapi-staff-points-validate-link-token');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-staff-points-validate-link-token" onclick="cancelTryOut('POSTapi-staff-points-validate-link-token');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-staff-points-validate-link-token" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/staff/points/validate-link-token</code></b>
</p>
<p>
<label id="auth-POSTapi-staff-points-validate-link-token" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-staff-points-validate-link-token" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-staff-points-validate-link-token" data-component="query" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>token</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="token" data-endpoint="POSTapi-staff-points-validate-link-token" data-component="query" required  hidden>
<br>
token of user.
</p>
</form>


## Push credited points to user using token (qr code)

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/staff/points/push/credit" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","token":"aut","points":14,"segments":[15,18]}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/points/push/credit"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "token": "aut",
    "points": 14,
    "segments": [
        15,
        18
    ]
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
    'https://loyalty.web/api/staff/points/push/credit',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'token' => 'aut',
            'points' => 14,
            'segments' => [
                15,
                18,
            ],
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-staff-points-push-credit" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-staff-points-push-credit"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-staff-points-push-credit"></code></pre>
</div>
<div id="execution-error-POSTapi-staff-points-push-credit" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-staff-points-push-credit"></code></pre>
</div>
<form id="form-POSTapi-staff-points-push-credit" data-method="POST" data-path="api/staff/points/push/credit" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-staff-points-push-credit', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-staff-points-push-credit" onclick="tryItOut('POSTapi-staff-points-push-credit');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-staff-points-push-credit" onclick="cancelTryOut('POSTapi-staff-points-push-credit');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-staff-points-push-credit" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/staff/points/push/credit</code></b>
</p>
<p>
<label id="auth-POSTapi-staff-points-push-credit" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-staff-points-push-credit" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-staff-points-push-credit" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>token</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="token" data-endpoint="POSTapi-staff-points-push-credit" data-component="body" required  hidden>
<br>
token of user.
</p>
<p>
<b><code>points</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
<input type="number" name="points" data-endpoint="POSTapi-staff-points-push-credit" data-component="body" required  hidden>
<br>
amount of point for credited to user.
</p>
<p>
<b><code>segments</code></b>&nbsp;&nbsp;<small>integer[]</small>     <i>optional</i> &nbsp;
<input type="number" name="segments.0" data-endpoint="POSTapi-staff-points-push-credit" data-component="body"  hidden>
<input type="number" name="segments.1" data-endpoint="POSTapi-staff-points-push-credit" data-component="body" hidden>
<br>
id of segments.
</p>

</form>


## Get active customer code(s)

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/staff/points/customer/active-codes" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/points/customer/active-codes"
);

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
    'https://loyalty.web/api/staff/points/customer/active-codes',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
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
<div id="execution-results-GETapi-staff-points-customer-active-codes" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-staff-points-customer-active-codes"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-staff-points-customer-active-codes"></code></pre>
</div>
<div id="execution-error-GETapi-staff-points-customer-active-codes" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-staff-points-customer-active-codes"></code></pre>
</div>
<form id="form-GETapi-staff-points-customer-active-codes" data-method="GET" data-path="api/staff/points/customer/active-codes" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-staff-points-customer-active-codes', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-staff-points-customer-active-codes" onclick="tryItOut('GETapi-staff-points-customer-active-codes');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-staff-points-customer-active-codes" onclick="cancelTryOut('GETapi-staff-points-customer-active-codes');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-staff-points-customer-active-codes" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/staff/points/customer/active-codes</code></b>
</p>
<p>
<label id="auth-GETapi-staff-points-customer-active-codes" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-staff-points-customer-active-codes" data-component="header"></label>
</p>
</form>


## Generate easy to remember code for merchant

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/staff/points/customer/generate-code" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","expires":"maxime","bill_amount":7,"bill_number":15,"segments":[10,8]}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/points/customer/generate-code"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "expires": "maxime",
    "bill_amount": 7,
    "bill_number": 15,
    "segments": [
        10,
        8
    ]
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
    'https://loyalty.web/api/staff/points/customer/generate-code',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'expires' => 'maxime',
            'bill_amount' => 7,
            'bill_number' => 15,
            'segments' => [
                10,
                8,
            ],
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-staff-points-customer-generate-code" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-staff-points-customer-generate-code"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-staff-points-customer-generate-code"></code></pre>
</div>
<div id="execution-error-POSTapi-staff-points-customer-generate-code" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-staff-points-customer-generate-code"></code></pre>
</div>
<form id="form-POSTapi-staff-points-customer-generate-code" data-method="POST" data-path="api/staff/points/customer/generate-code" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-staff-points-customer-generate-code', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-staff-points-customer-generate-code" onclick="tryItOut('POSTapi-staff-points-customer-generate-code');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-staff-points-customer-generate-code" onclick="cancelTryOut('POSTapi-staff-points-customer-generate-code');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-staff-points-customer-generate-code" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/staff/points/customer/generate-code</code></b>
</p>
<p>
<label id="auth-POSTapi-staff-points-customer-generate-code" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-staff-points-customer-generate-code" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-staff-points-customer-generate-code" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>expires</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="expires" data-endpoint="POSTapi-staff-points-customer-generate-code" data-component="body" required  hidden>
<br>
following value hour , day , week , month.
</p>
<p>
<b><code>bill_amount</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
<input type="number" name="bill_amount" data-endpoint="POSTapi-staff-points-customer-generate-code" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>bill_number</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
<input type="number" name="bill_number" data-endpoint="POSTapi-staff-points-customer-generate-code" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>segments</code></b>&nbsp;&nbsp;<small>integer[]</small>     <i>optional</i> &nbsp;
<input type="number" name="segments.0" data-endpoint="POSTapi-staff-points-customer-generate-code" data-component="body"  hidden>
<input type="number" name="segments.1" data-endpoint="POSTapi-staff-points-customer-generate-code" data-component="body" hidden>
<br>
id of segments.
</p>

</form>


## Get active merchant code

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/staff/points/merchant/active-code" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/points/merchant/active-code"
);

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
    'https://loyalty.web/api/staff/points/merchant/active-code',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
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
<div id="execution-results-GETapi-staff-points-merchant-active-code" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-staff-points-merchant-active-code"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-staff-points-merchant-active-code"></code></pre>
</div>
<div id="execution-error-GETapi-staff-points-merchant-active-code" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-staff-points-merchant-active-code"></code></pre>
</div>
<form id="form-GETapi-staff-points-merchant-active-code" data-method="GET" data-path="api/staff/points/merchant/active-code" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-staff-points-merchant-active-code', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-staff-points-merchant-active-code" onclick="tryItOut('GETapi-staff-points-merchant-active-code');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-staff-points-merchant-active-code" onclick="cancelTryOut('GETapi-staff-points-merchant-active-code');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-staff-points-merchant-active-code" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/staff/points/merchant/active-code</code></b>
</p>
<p>
<label id="auth-GETapi-staff-points-merchant-active-code" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-staff-points-merchant-active-code" data-component="header"></label>
</p>
</form>


## Generate easy to remember code for merchant

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/staff/points/merchant/generate-code" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","expires":"corrupti","segments":[6,7]}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/points/merchant/generate-code"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "expires": "corrupti",
    "segments": [
        6,
        7
    ]
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
    'https://loyalty.web/api/staff/points/merchant/generate-code',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'expires' => 'corrupti',
            'segments' => [
                6,
                7,
            ],
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-staff-points-merchant-generate-code" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-staff-points-merchant-generate-code"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-staff-points-merchant-generate-code"></code></pre>
</div>
<div id="execution-error-POSTapi-staff-points-merchant-generate-code" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-staff-points-merchant-generate-code"></code></pre>
</div>
<form id="form-POSTapi-staff-points-merchant-generate-code" data-method="POST" data-path="api/staff/points/merchant/generate-code" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-staff-points-merchant-generate-code', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-staff-points-merchant-generate-code" onclick="tryItOut('POSTapi-staff-points-merchant-generate-code');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-staff-points-merchant-generate-code" onclick="cancelTryOut('POSTapi-staff-points-merchant-generate-code');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-staff-points-merchant-generate-code" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/staff/points/merchant/generate-code</code></b>
</p>
<p>
<label id="auth-POSTapi-staff-points-merchant-generate-code" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-staff-points-merchant-generate-code" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-staff-points-merchant-generate-code" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>expires</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="expires" data-endpoint="POSTapi-staff-points-merchant-generate-code" data-component="body" required  hidden>
<br>
following value hour , day , week , month.
</p>
<p>
<b><code>segments</code></b>&nbsp;&nbsp;<small>integer[]</small>     <i>optional</i> &nbsp;
<input type="number" name="segments.0" data-endpoint="POSTapi-staff-points-merchant-generate-code" data-component="body"  hidden>
<input type="number" name="segments.1" data-endpoint="POSTapi-staff-points-merchant-generate-code" data-component="body" hidden>
<br>
id of segments.
</p>

</form>


## Credit customer by number

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/staff/points/customer/credit" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","number":"repellat","bill_amount":18,"bill_number":3,"segments":[12,15]}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/points/customer/credit"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "number": "repellat",
    "bill_amount": 18,
    "bill_number": 3,
    "segments": [
        12,
        15
    ]
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
    'https://loyalty.web/api/staff/points/customer/credit',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'number' => 'repellat',
            'bill_amount' => 18,
            'bill_number' => 3,
            'segments' => [
                12,
                15,
            ],
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-staff-points-customer-credit" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-staff-points-customer-credit"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-staff-points-customer-credit"></code></pre>
</div>
<div id="execution-error-POSTapi-staff-points-customer-credit" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-staff-points-customer-credit"></code></pre>
</div>
<form id="form-POSTapi-staff-points-customer-credit" data-method="POST" data-path="api/staff/points/customer/credit" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-staff-points-customer-credit', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-staff-points-customer-credit" onclick="tryItOut('POSTapi-staff-points-customer-credit');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-staff-points-customer-credit" onclick="cancelTryOut('POSTapi-staff-points-customer-credit');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-staff-points-customer-credit" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/staff/points/customer/credit</code></b>
</p>
<p>
<label id="auth-POSTapi-staff-points-customer-credit" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-staff-points-customer-credit" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-staff-points-customer-credit" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>number</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="number" data-endpoint="POSTapi-staff-points-customer-credit" data-component="body" required  hidden>
<br>
customer number.
</p>
<p>
<b><code>bill_amount</code></b>&nbsp;&nbsp;<small>integer</small>     <i>optional</i> &nbsp;
<input type="number" name="bill_amount" data-endpoint="POSTapi-staff-points-customer-credit" data-component="body"  hidden>
<br>
required.
</p>
<p>
<b><code>bill_number</code></b>&nbsp;&nbsp;<small>integer</small>     <i>optional</i> &nbsp;
<input type="number" name="bill_number" data-endpoint="POSTapi-staff-points-customer-credit" data-component="body"  hidden>
<br>
required.
</p>
<p>
<b><code>segments</code></b>&nbsp;&nbsp;<small>integer[]</small>     <i>optional</i> &nbsp;
<input type="number" name="segments.0" data-endpoint="POSTapi-staff-points-customer-credit" data-component="body"  hidden>
<input type="number" name="segments.1" data-endpoint="POSTapi-staff-points-customer-credit" data-component="body" hidden>
<br>
id of segments.
</p>

</form>



