# Customer Auth

Endpoints for customer authentication

## Handle user registration.




> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/campaign/auth/register" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","name":"John Doe","email":"johndoe@internet.com","country_isd_code":62,"customer_number":8123456789,"password":"password","terms":true}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/auth/register"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "name": "John Doe",
    "email": "johndoe@internet.com",
    "country_isd_code": 62,
    "customer_number": 8123456789,
    "password": "password",
    "terms": true
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
    'https://loyalty.web/api/campaign/auth/register',
    [
        'headers' => [
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'name' => 'John Doe',
            'email' => 'johndoe@internet.com',
            'country_isd_code' => 62,
            'customer_number' => 8123456789,
            'password' => 'password',
            'terms' => true,
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-campaign-auth-register" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-campaign-auth-register"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-campaign-auth-register"></code></pre>
</div>
<div id="execution-error-POSTapi-campaign-auth-register" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-campaign-auth-register"></code></pre>
</div>
<form id="form-POSTapi-campaign-auth-register" data-method="POST" data-path="api/campaign/auth/register" data-authed="0" data-hasfiles="0" data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-campaign-auth-register', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-campaign-auth-register" onclick="tryItOut('POSTapi-campaign-auth-register');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-campaign-auth-register" onclick="cancelTryOut('POSTapi-campaign-auth-register');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-campaign-auth-register" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/campaign/auth/register</code></b>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-campaign-auth-register" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>name</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="name" data-endpoint="POSTapi-campaign-auth-register" data-component="body" required  hidden>
<br>
Full name of the user.
</p>
<p>
<b><code>email</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="email" data-endpoint="POSTapi-campaign-auth-register" data-component="body" required  hidden>
<br>
User's email.
</p>
<p>
<b><code>country_isd_code</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
<input type="number" name="country_isd_code" data-endpoint="POSTapi-campaign-auth-register" data-component="body" required  hidden>
<br>
User's phone number.
</p>
<p>
<b><code>customer_number</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
<input type="number" name="customer_number" data-endpoint="POSTapi-campaign-auth-register" data-component="body" required  hidden>
<br>
User's phone number.
</p>
<p>
<b><code>password</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="password" name="password" data-endpoint="POSTapi-campaign-auth-register" data-component="body" required  hidden>
<br>
Password.
</p>
<p>
<b><code>terms</code></b>&nbsp;&nbsp;<small>boolean</small>  &nbsp;
<label data-endpoint="POSTapi-campaign-auth-register" hidden><input type="radio" name="terms" value="true" data-endpoint="POSTapi-campaign-auth-register" data-component="body" required ><code>true</code></label>
<label data-endpoint="POSTapi-campaign-auth-register" hidden><input type="radio" name="terms" value="false" data-endpoint="POSTapi-campaign-auth-register" data-component="body" required ><code>false</code></label>
<br>
: 0, 1
</p>

</form>


## Handle user login.




> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/campaign/auth/loginApp" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"email":"johndoe@internet.com","password":"password"}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/auth/loginApp"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "johndoe@internet.com",
    "password": "password"
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
    'https://loyalty.web/api/campaign/auth/loginApp',
    [
        'headers' => [
            'Accept' => 'application/json',
        ],
        'json' => [
            'email' => 'johndoe@internet.com',
            'password' => 'password',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-campaign-auth-loginApp" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-campaign-auth-loginApp"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-campaign-auth-loginApp"></code></pre>
</div>
<div id="execution-error-POSTapi-campaign-auth-loginApp" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-campaign-auth-loginApp"></code></pre>
</div>
<form id="form-POSTapi-campaign-auth-loginApp" data-method="POST" data-path="api/campaign/auth/loginApp" data-authed="0" data-hasfiles="0" data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-campaign-auth-loginApp', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-campaign-auth-loginApp" onclick="tryItOut('POSTapi-campaign-auth-loginApp');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-campaign-auth-loginApp" onclick="cancelTryOut('POSTapi-campaign-auth-loginApp');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-campaign-auth-loginApp" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/campaign/auth/loginApp</code></b>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>email</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="email" data-endpoint="POSTapi-campaign-auth-loginApp" data-component="body" required  hidden>
<br>
User's email.
</p>
<p>
<b><code>password</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="password" name="password" data-endpoint="POSTapi-campaign-auth-loginApp" data-component="body" required  hidden>
<br>
Password.
</p>

</form>


## Refresh authorization token.

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/campaign/auth/refresh" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/auth/refresh"
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
    'https://loyalty.web/api/campaign/auth/refresh',
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


> Example response (401):

```json
{
    "error": "refresh_token_error"
}
```
<div id="execution-results-GETapi-campaign-auth-refresh" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-campaign-auth-refresh"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-campaign-auth-refresh"></code></pre>
</div>
<div id="execution-error-GETapi-campaign-auth-refresh" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-campaign-auth-refresh"></code></pre>
</div>
<form id="form-GETapi-campaign-auth-refresh" data-method="GET" data-path="api/campaign/auth/refresh" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-campaign-auth-refresh', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-campaign-auth-refresh" onclick="tryItOut('GETapi-campaign-auth-refresh');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-campaign-auth-refresh" onclick="cancelTryOut('GETapi-campaign-auth-refresh');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-campaign-auth-refresh" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/campaign/auth/refresh</code></b>
</p>
<p>
<label id="auth-GETapi-campaign-auth-refresh" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-campaign-auth-refresh" data-component="header"></label>
</p>
</form>


## Send a password reset email.




> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/campaign/auth/password/reset" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","email":"johndoe@internet.com"}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/auth/password/reset"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "email": "johndoe@internet.com"
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
    'https://loyalty.web/api/campaign/auth/password/reset',
    [
        'headers' => [
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'email' => 'johndoe@internet.com',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-campaign-auth-password-reset" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-campaign-auth-password-reset"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-campaign-auth-password-reset"></code></pre>
</div>
<div id="execution-error-POSTapi-campaign-auth-password-reset" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-campaign-auth-password-reset"></code></pre>
</div>
<form id="form-POSTapi-campaign-auth-password-reset" data-method="POST" data-path="api/campaign/auth/password/reset" data-authed="0" data-hasfiles="0" data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-campaign-auth-password-reset', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-campaign-auth-password-reset" onclick="tryItOut('POSTapi-campaign-auth-password-reset');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-campaign-auth-password-reset" onclick="cancelTryOut('POSTapi-campaign-auth-password-reset');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-campaign-auth-password-reset" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/campaign/auth/password/reset</code></b>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-campaign-auth-password-reset" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>email</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="email" data-endpoint="POSTapi-campaign-auth-password-reset" data-component="body" required  hidden>
<br>
User's email.
</p>

</form>


## Get user info .

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/campaign/auth/user?uuid=tenetur" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/auth/user"
);

let params = {
    "uuid": "tenetur",
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
    'https://loyalty.web/api/campaign/auth/user',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'query' => [
            'uuid'=> 'tenetur',
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
<div id="execution-results-GETapi-campaign-auth-user" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-campaign-auth-user"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-campaign-auth-user"></code></pre>
</div>
<div id="execution-error-GETapi-campaign-auth-user" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-campaign-auth-user"></code></pre>
</div>
<form id="form-GETapi-campaign-auth-user" data-method="GET" data-path="api/campaign/auth/user" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-campaign-auth-user', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-campaign-auth-user" onclick="tryItOut('GETapi-campaign-auth-user');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-campaign-auth-user" onclick="cancelTryOut('GETapi-campaign-auth-user');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-campaign-auth-user" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/campaign/auth/user</code></b>
</p>
<p>
<label id="auth-GETapi-campaign-auth-user" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-campaign-auth-user" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="GETapi-campaign-auth-user" data-component="query" required  hidden>
<br>
uuid of website.
</p>
</form>


## Get detailed joined website of user.

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/campaign/auth/website" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/auth/website"
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
    'https://loyalty.web/api/campaign/auth/website',
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
<div id="execution-results-GETapi-campaign-auth-website" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-campaign-auth-website"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-campaign-auth-website"></code></pre>
</div>
<div id="execution-error-GETapi-campaign-auth-website" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-campaign-auth-website"></code></pre>
</div>
<form id="form-GETapi-campaign-auth-website" data-method="GET" data-path="api/campaign/auth/website" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-campaign-auth-website', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-campaign-auth-website" onclick="tryItOut('GETapi-campaign-auth-website');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-campaign-auth-website" onclick="cancelTryOut('GETapi-campaign-auth-website');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-campaign-auth-website" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/campaign/auth/website</code></b>
</p>
<p>
<label id="auth-GETapi-campaign-auth-website" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-campaign-auth-website" data-component="header"></label>
</p>
</form>


## Handle user logout.

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/campaign/auth/logout" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/auth/logout"
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
    'https://loyalty.web/api/campaign/auth/logout',
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


<div id="execution-results-POSTapi-campaign-auth-logout" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-campaign-auth-logout"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-campaign-auth-logout"></code></pre>
</div>
<div id="execution-error-POSTapi-campaign-auth-logout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-campaign-auth-logout"></code></pre>
</div>
<form id="form-POSTapi-campaign-auth-logout" data-method="POST" data-path="api/campaign/auth/logout" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-campaign-auth-logout', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-campaign-auth-logout" onclick="tryItOut('POSTapi-campaign-auth-logout');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-campaign-auth-logout" onclick="cancelTryOut('POSTapi-campaign-auth-logout');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-campaign-auth-logout" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/campaign/auth/logout</code></b>
</p>
<p>
<label id="auth-POSTapi-campaign-auth-logout" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-campaign-auth-logout" data-component="header"></label>
</p>
</form>


## Update profile.

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/campaign/auth/profile" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","name":"John Doe","email":"johndoe@internet.com","new_password":"password","current_password":"password"}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/auth/profile"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "name": "John Doe",
    "email": "johndoe@internet.com",
    "new_password": "password",
    "current_password": "password"
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
    'https://loyalty.web/api/campaign/auth/profile',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'name' => 'John Doe',
            'email' => 'johndoe@internet.com',
            'new_password' => 'password',
            'current_password' => 'password',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-campaign-auth-profile" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-campaign-auth-profile"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-campaign-auth-profile"></code></pre>
</div>
<div id="execution-error-POSTapi-campaign-auth-profile" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-campaign-auth-profile"></code></pre>
</div>
<form id="form-POSTapi-campaign-auth-profile" data-method="POST" data-path="api/campaign/auth/profile" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-campaign-auth-profile', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-campaign-auth-profile" onclick="tryItOut('POSTapi-campaign-auth-profile');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-campaign-auth-profile" onclick="cancelTryOut('POSTapi-campaign-auth-profile');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-campaign-auth-profile" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/campaign/auth/profile</code></b>
</p>
<p>
<label id="auth-POSTapi-campaign-auth-profile" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-campaign-auth-profile" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-campaign-auth-profile" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>name</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="name" data-endpoint="POSTapi-campaign-auth-profile" data-component="body" required  hidden>
<br>
Full name of the user.
</p>
<p>
<b><code>email</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="email" data-endpoint="POSTapi-campaign-auth-profile" data-component="body" required  hidden>
<br>
User's email.
</p>
<p>
<b><code>new_password</code></b>&nbsp;&nbsp;<small>string</small>     <i>optional</i> &nbsp;
<input type="password" name="new_password" data-endpoint="POSTapi-campaign-auth-profile" data-component="body"  hidden>
<br>
New Password leave empty if you don't want to change the password.
</p>
<p>
<b><code>current_password</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="password" name="current_password" data-endpoint="POSTapi-campaign-auth-profile" data-component="body" required  hidden>
<br>
Current Password.
</p>

</form>


## Request OTP for customer for any purpose

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/campaign/auth/otp?uuid=283ca865-a71c-4d4a-b8cb-8c46c5b3ca57&purpose=est" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/auth/otp"
);

let params = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "purpose": "est",
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
    'https://loyalty.web/api/campaign/auth/otp',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'query' => [
            'uuid'=> '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'purpose'=> 'est',
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
<div id="execution-results-GETapi-campaign-auth-otp" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-campaign-auth-otp"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-campaign-auth-otp"></code></pre>
</div>
<div id="execution-error-GETapi-campaign-auth-otp" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-campaign-auth-otp"></code></pre>
</div>
<form id="form-GETapi-campaign-auth-otp" data-method="GET" data-path="api/campaign/auth/otp" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-campaign-auth-otp', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-campaign-auth-otp" onclick="tryItOut('GETapi-campaign-auth-otp');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-campaign-auth-otp" onclick="cancelTryOut('GETapi-campaign-auth-otp');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-campaign-auth-otp" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/campaign/auth/otp</code></b>
</p>
<p>
<label id="auth-GETapi-campaign-auth-otp" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-campaign-auth-otp" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="GETapi-campaign-auth-otp" data-component="query" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>purpose</code></b>&nbsp;&nbsp;<small>string</small>     <i>optional</i> &nbsp;
<input type="text" name="purpose" data-endpoint="GETapi-campaign-auth-otp" data-component="query"  hidden>
<br>
string.
</p>
</form>


## Verify OTP code

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/campaign/auth/otp" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"code":"et"}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/auth/otp"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "code": "et"
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
    'https://loyalty.web/api/campaign/auth/otp',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'code' => 'et',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-campaign-auth-otp" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-campaign-auth-otp"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-campaign-auth-otp"></code></pre>
</div>
<div id="execution-error-POSTapi-campaign-auth-otp" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-campaign-auth-otp"></code></pre>
</div>
<form id="form-POSTapi-campaign-auth-otp" data-method="POST" data-path="api/campaign/auth/otp" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-campaign-auth-otp', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-campaign-auth-otp" onclick="tryItOut('POSTapi-campaign-auth-otp');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-campaign-auth-otp" onclick="cancelTryOut('POSTapi-campaign-auth-otp');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-campaign-auth-otp" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/campaign/auth/otp</code></b>
</p>
<p>
<label id="auth-POSTapi-campaign-auth-otp" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-campaign-auth-otp" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>code</code></b>&nbsp;&nbsp;<small>required</small>     <i>optional</i> &nbsp;
<input type="text" name="code" data-endpoint="POSTapi-campaign-auth-otp" data-component="body"  hidden>
<br>
Otp Code.
</p>

</form>



