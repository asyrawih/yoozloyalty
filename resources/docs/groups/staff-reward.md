# Staff Reward

Endpoints for staff related to reward redemption procedures.

## Get all rewards from campaign

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/staff/rewards?uuid=283ca865-a71c-4d4a-b8cb-8c46c5b3ca57" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/rewards"
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
    'https://loyalty.web/api/staff/rewards',
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
<div id="execution-results-GETapi-staff-rewards" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-staff-rewards"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-staff-rewards"></code></pre>
</div>
<div id="execution-error-GETapi-staff-rewards" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-staff-rewards"></code></pre>
</div>
<form id="form-GETapi-staff-rewards" data-method="GET" data-path="api/staff/rewards" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-staff-rewards', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-staff-rewards" onclick="tryItOut('GETapi-staff-rewards');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-staff-rewards" onclick="cancelTryOut('GETapi-staff-rewards');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-staff-rewards" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/staff/rewards</code></b>
</p>
<p>
<label id="auth-GETapi-staff-rewards" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-staff-rewards" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="GETapi-staff-rewards" data-component="query" required  hidden>
<br>
uuid of website.
</p>
</form>


## Validate if link token is (still) valid

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/staff/rewards/validate-link-token?uuid=283ca865-a71c-4d4a-b8cb-8c46c5b3ca57&token=reiciendis" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/rewards/validate-link-token"
);

let params = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "token": "reiciendis",
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
    'https://loyalty.web/api/staff/rewards/validate-link-token',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'query' => [
            'uuid'=> '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'token'=> 'reiciendis',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-staff-rewards-validate-link-token" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-staff-rewards-validate-link-token"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-staff-rewards-validate-link-token"></code></pre>
</div>
<div id="execution-error-POSTapi-staff-rewards-validate-link-token" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-staff-rewards-validate-link-token"></code></pre>
</div>
<form id="form-POSTapi-staff-rewards-validate-link-token" data-method="POST" data-path="api/staff/rewards/validate-link-token" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-staff-rewards-validate-link-token', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-staff-rewards-validate-link-token" onclick="tryItOut('POSTapi-staff-rewards-validate-link-token');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-staff-rewards-validate-link-token" onclick="cancelTryOut('POSTapi-staff-rewards-validate-link-token');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-staff-rewards-validate-link-token" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/staff/rewards/validate-link-token</code></b>
</p>
<p>
<label id="auth-POSTapi-staff-rewards-validate-link-token" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-staff-rewards-validate-link-token" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-staff-rewards-validate-link-token" data-component="query" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>token</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="token" data-endpoint="POSTapi-staff-rewards-validate-link-token" data-component="query" required  hidden>
<br>
token of user.
</p>
</form>


## Push redeemed reward to broadcast channel

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/staff/rewards/push/redemption" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","token":"exercitationem","reward":"703fcac9-9ffa-4527-9b3d-c9549e02a353","segments":[4,11]}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/rewards/push/redemption"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "token": "exercitationem",
    "reward": "703fcac9-9ffa-4527-9b3d-c9549e02a353",
    "segments": [
        4,
        11
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
    'https://loyalty.web/api/staff/rewards/push/redemption',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'token' => 'exercitationem',
            'reward' => '703fcac9-9ffa-4527-9b3d-c9549e02a353',
            'segments' => [
                4,
                11,
            ],
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-staff-rewards-push-redemption" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-staff-rewards-push-redemption"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-staff-rewards-push-redemption"></code></pre>
</div>
<div id="execution-error-POSTapi-staff-rewards-push-redemption" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-staff-rewards-push-redemption"></code></pre>
</div>
<form id="form-POSTapi-staff-rewards-push-redemption" data-method="POST" data-path="api/staff/rewards/push/redemption" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-staff-rewards-push-redemption', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-staff-rewards-push-redemption" onclick="tryItOut('POSTapi-staff-rewards-push-redemption');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-staff-rewards-push-redemption" onclick="cancelTryOut('POSTapi-staff-rewards-push-redemption');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-staff-rewards-push-redemption" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/staff/rewards/push/redemption</code></b>
</p>
<p>
<label id="auth-POSTapi-staff-rewards-push-redemption" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-staff-rewards-push-redemption" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-staff-rewards-push-redemption" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>token</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="token" data-endpoint="POSTapi-staff-rewards-push-redemption" data-component="body" required  hidden>
<br>
token of user.
</p>
<p>
<b><code>reward</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="reward" data-endpoint="POSTapi-staff-rewards-push-redemption" data-component="body" required  hidden>
<br>
uuid of rewards.
</p>
<p>
<b><code>segments</code></b>&nbsp;&nbsp;<small>integer[]</small>     <i>optional</i> &nbsp;
<input type="number" name="segments.0" data-endpoint="POSTapi-staff-rewards-push-redemption" data-component="body"  hidden>
<input type="number" name="segments.1" data-endpoint="POSTapi-staff-rewards-push-redemption" data-component="body" hidden>
<br>
id of segments.
</p>

</form>


## Get active merchant code

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/staff/rewards/merchant/active-code" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/rewards/merchant/active-code"
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
    'https://loyalty.web/api/staff/rewards/merchant/active-code',
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
<div id="execution-results-GETapi-staff-rewards-merchant-active-code" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-staff-rewards-merchant-active-code"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-staff-rewards-merchant-active-code"></code></pre>
</div>
<div id="execution-error-GETapi-staff-rewards-merchant-active-code" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-staff-rewards-merchant-active-code"></code></pre>
</div>
<form id="form-GETapi-staff-rewards-merchant-active-code" data-method="GET" data-path="api/staff/rewards/merchant/active-code" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-staff-rewards-merchant-active-code', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-staff-rewards-merchant-active-code" onclick="tryItOut('GETapi-staff-rewards-merchant-active-code');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-staff-rewards-merchant-active-code" onclick="cancelTryOut('GETapi-staff-rewards-merchant-active-code');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-staff-rewards-merchant-active-code" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/staff/rewards/merchant/active-code</code></b>
</p>
<p>
<label id="auth-GETapi-staff-rewards-merchant-active-code" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-staff-rewards-merchant-active-code" data-component="header"></label>
</p>
</form>


## Generate easy to remember code for merchant

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/staff/rewards/merchant/generate-code" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","expires":"doloremque"}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/rewards/merchant/generate-code"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "expires": "doloremque"
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
    'https://loyalty.web/api/staff/rewards/merchant/generate-code',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'expires' => 'doloremque',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-staff-rewards-merchant-generate-code" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-staff-rewards-merchant-generate-code"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-staff-rewards-merchant-generate-code"></code></pre>
</div>
<div id="execution-error-POSTapi-staff-rewards-merchant-generate-code" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-staff-rewards-merchant-generate-code"></code></pre>
</div>
<form id="form-POSTapi-staff-rewards-merchant-generate-code" data-method="POST" data-path="api/staff/rewards/merchant/generate-code" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-staff-rewards-merchant-generate-code', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-staff-rewards-merchant-generate-code" onclick="tryItOut('POSTapi-staff-rewards-merchant-generate-code');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-staff-rewards-merchant-generate-code" onclick="cancelTryOut('POSTapi-staff-rewards-merchant-generate-code');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-staff-rewards-merchant-generate-code" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/staff/rewards/merchant/generate-code</code></b>
</p>
<p>
<label id="auth-POSTapi-staff-rewards-merchant-generate-code" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-staff-rewards-merchant-generate-code" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-staff-rewards-merchant-generate-code" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>expires</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="expires" data-endpoint="POSTapi-staff-rewards-merchant-generate-code" data-component="body" required  hidden>
<br>
following value hour , day , week , month.
</p>

</form>


## Redeem reward with customer number

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/staff/rewards/customer/credit" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","reward":"703fcac9-9ffa-4527-9b3d-c9549e02a353","mode":"nisi","number":"unde","segments":[5,2]}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/rewards/customer/credit"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "reward": "703fcac9-9ffa-4527-9b3d-c9549e02a353",
    "mode": "nisi",
    "number": "unde",
    "segments": [
        5,
        2
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
    'https://loyalty.web/api/staff/rewards/customer/credit',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'reward' => '703fcac9-9ffa-4527-9b3d-c9549e02a353',
            'mode' => 'nisi',
            'number' => 'unde',
            'segments' => [
                5,
                2,
            ],
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-staff-rewards-customer-credit" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-staff-rewards-customer-credit"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-staff-rewards-customer-credit"></code></pre>
</div>
<div id="execution-error-POSTapi-staff-rewards-customer-credit" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-staff-rewards-customer-credit"></code></pre>
</div>
<form id="form-POSTapi-staff-rewards-customer-credit" data-method="POST" data-path="api/staff/rewards/customer/credit" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-staff-rewards-customer-credit', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-staff-rewards-customer-credit" onclick="tryItOut('POSTapi-staff-rewards-customer-credit');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-staff-rewards-customer-credit" onclick="cancelTryOut('POSTapi-staff-rewards-customer-credit');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-staff-rewards-customer-credit" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/staff/rewards/customer/credit</code></b>
</p>
<p>
<label id="auth-POSTapi-staff-rewards-customer-credit" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-staff-rewards-customer-credit" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-staff-rewards-customer-credit" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>reward</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="reward" data-endpoint="POSTapi-staff-rewards-customer-credit" data-component="body" required  hidden>
<br>
uuid of rewards.
</p>
<p>
<b><code>mode</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="mode" data-endpoint="POSTapi-staff-rewards-customer-credit" data-component="body" required  hidden>
<br>
following value number, card_number.
</p>
<p>
<b><code>number</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="number" data-endpoint="POSTapi-staff-rewards-customer-credit" data-component="body" required  hidden>
<br>
customer number or card_number.
</p>
<p>
<b><code>segments</code></b>&nbsp;&nbsp;<small>integer[]</small>     <i>optional</i> &nbsp;
<input type="number" name="segments.0" data-endpoint="POSTapi-staff-rewards-customer-credit" data-component="body"  hidden>
<input type="number" name="segments.1" data-endpoint="POSTapi-staff-rewards-customer-credit" data-component="body" hidden>
<br>
id of segments.
</p>

</form>



