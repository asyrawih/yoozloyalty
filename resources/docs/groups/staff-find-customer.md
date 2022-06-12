# Staff find Customer

Endpoint for retrieving customer by phone number

## Get customer by phone number

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/staff/customer/18" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/customer/18"
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
    'https://loyalty.web/api/staff/customer/18',
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
<div id="execution-results-GETapi-staff-customer--phone_number-" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-staff-customer--phone_number-"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-staff-customer--phone_number-"></code></pre>
</div>
<div id="execution-error-GETapi-staff-customer--phone_number-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-staff-customer--phone_number-"></code></pre>
</div>
<form id="form-GETapi-staff-customer--phone_number-" data-method="GET" data-path="api/staff/customer/{phone_number}" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-staff-customer--phone_number-', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-staff-customer--phone_number-" onclick="tryItOut('GETapi-staff-customer--phone_number-');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-staff-customer--phone_number-" onclick="cancelTryOut('GETapi-staff-customer--phone_number-');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-staff-customer--phone_number-" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/staff/customer/{phone_number}</code></b>
</p>
<p>
<label id="auth-GETapi-staff-customer--phone_number-" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-staff-customer--phone_number-" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
<p>
<b><code>phone_number</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
<input type="number" name="phone_number" data-endpoint="GETapi-staff-customer--phone_number-" data-component="url" required  hidden>
<br>
The number of the customer.
</p>
</form>



