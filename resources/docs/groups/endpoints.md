# Endpoints


## Get admin notif unred.

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/campaign/notifUnread" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/notifUnread"
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
    'https://loyalty.web/api/campaign/notifUnread',
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
<div id="execution-results-GETapi-campaign-notifUnread" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-campaign-notifUnread"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-campaign-notifUnread"></code></pre>
</div>
<div id="execution-error-GETapi-campaign-notifUnread" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-campaign-notifUnread"></code></pre>
</div>
<form id="form-GETapi-campaign-notifUnread" data-method="GET" data-path="api/campaign/notifUnread" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-campaign-notifUnread', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-campaign-notifUnread" onclick="tryItOut('GETapi-campaign-notifUnread');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-campaign-notifUnread" onclick="cancelTryOut('GETapi-campaign-notifUnread');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-campaign-notifUnread" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/campaign/notifUnread</code></b>
</p>
<p>
<label id="auth-GETapi-campaign-notifUnread" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-campaign-notifUnread" data-component="header"></label>
</p>
</form>


## Post mask as read admin notif.

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/campaign/maskAsRead" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/maskAsRead"
);

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
    'https://loyalty.web/api/campaign/maskAsRead',
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


<div id="execution-results-POSTapi-campaign-maskAsRead" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-campaign-maskAsRead"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-campaign-maskAsRead"></code></pre>
</div>
<div id="execution-error-POSTapi-campaign-maskAsRead" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-campaign-maskAsRead"></code></pre>
</div>
<form id="form-POSTapi-campaign-maskAsRead" data-method="POST" data-path="api/campaign/maskAsRead" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-campaign-maskAsRead', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-campaign-maskAsRead" onclick="tryItOut('POSTapi-campaign-maskAsRead');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-campaign-maskAsRead" onclick="cancelTryOut('POSTapi-campaign-maskAsRead');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-campaign-maskAsRead" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/campaign/maskAsRead</code></b>
</p>
<p>
<label id="auth-POSTapi-campaign-maskAsRead" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-campaign-maskAsRead" data-component="header"></label>
</p>
</form>



