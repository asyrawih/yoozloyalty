# Customer Request Credit 
APIs for customer request credit

## Customer request credit

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/campaign/credit-request" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","bill_amount":16,"bill_number":17}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/credit-request"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "bill_amount": 16,
    "bill_number": 17
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
    'https://loyalty.web/api/campaign/credit-request',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'bill_amount' => 16,
            'bill_number' => 17,
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-campaign-credit-request" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-campaign-credit-request"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-campaign-credit-request"></code></pre>
</div>
<div id="execution-error-POSTapi-campaign-credit-request" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-campaign-credit-request"></code></pre>
</div>
<form id="form-POSTapi-campaign-credit-request" data-method="POST" data-path="api/campaign/credit-request" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-campaign-credit-request', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-campaign-credit-request" onclick="tryItOut('POSTapi-campaign-credit-request');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-campaign-credit-request" onclick="cancelTryOut('POSTapi-campaign-credit-request');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-campaign-credit-request" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/campaign/credit-request</code></b>
</p>
<p>
<label id="auth-POSTapi-campaign-credit-request" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-campaign-credit-request" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-campaign-credit-request" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>bill_amount</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
<input type="number" name="bill_amount" data-endpoint="POSTapi-campaign-credit-request" data-component="body" required  hidden>
<br>
points of request.
</p>
<p>
<b><code>bill_number</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
<input type="number" name="bill_number" data-endpoint="POSTapi-campaign-credit-request" data-component="body" required  hidden>
<br>
points of request.
</p>

</form>



