# Staff History

Endpoints for staff related to reward redemption history

## Get staff member history.

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/staff/history?uuid=283ca865-a71c-4d4a-b8cb-8c46c5b3ca57" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/history"
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
    'https://loyalty.web/api/staff/history',
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
<div id="execution-results-GETapi-staff-history" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-staff-history"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-staff-history"></code></pre>
</div>
<div id="execution-error-GETapi-staff-history" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-staff-history"></code></pre>
</div>
<form id="form-GETapi-staff-history" data-method="GET" data-path="api/staff/history" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-staff-history', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-staff-history" onclick="tryItOut('GETapi-staff-history');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-staff-history" onclick="cancelTryOut('GETapi-staff-history');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-staff-history" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/staff/history</code></b>
</p>
<p>
<label id="auth-GETapi-staff-history" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-staff-history" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="GETapi-staff-history" data-component="query" required  hidden>
<br>
uuid of website.
</p>
</form>



