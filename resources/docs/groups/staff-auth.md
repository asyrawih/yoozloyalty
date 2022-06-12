# Staff Auth

Endpoints for staff authentication

## Handle user login.




> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/staff/auth/login" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","email":"johndoe@internet.com","password":"password"}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/auth/login"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
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
    'https://loyalty.web/api/staff/auth/login',
    [
        'headers' => [
            'Accept' => 'application/json',
        ],
        'json' => [
            'uuid' => '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'email' => 'johndoe@internet.com',
            'password' => 'password',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-staff-auth-login" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-staff-auth-login"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-staff-auth-login"></code></pre>
</div>
<div id="execution-error-POSTapi-staff-auth-login" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-staff-auth-login"></code></pre>
</div>
<form id="form-POSTapi-staff-auth-login" data-method="POST" data-path="api/staff/auth/login" data-authed="0" data-hasfiles="0" data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-staff-auth-login', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-staff-auth-login" onclick="tryItOut('POSTapi-staff-auth-login');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-staff-auth-login" onclick="cancelTryOut('POSTapi-staff-auth-login');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-staff-auth-login" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/staff/auth/login</code></b>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-staff-auth-login" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>email</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="email" data-endpoint="POSTapi-staff-auth-login" data-component="body" required  hidden>
<br>
User's email.
</p>
<p>
<b><code>password</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="password" name="password" data-endpoint="POSTapi-staff-auth-login" data-component="body" required  hidden>
<br>
Password.
</p>

</form>


## Refresh authorization token.

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/staff/auth/refresh" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/auth/refresh"
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
    'https://loyalty.web/api/staff/auth/refresh',
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
<div id="execution-results-GETapi-staff-auth-refresh" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-staff-auth-refresh"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-staff-auth-refresh"></code></pre>
</div>
<div id="execution-error-GETapi-staff-auth-refresh" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-staff-auth-refresh"></code></pre>
</div>
<form id="form-GETapi-staff-auth-refresh" data-method="GET" data-path="api/staff/auth/refresh" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-staff-auth-refresh', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-staff-auth-refresh" onclick="tryItOut('GETapi-staff-auth-refresh');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-staff-auth-refresh" onclick="cancelTryOut('GETapi-staff-auth-refresh');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-staff-auth-refresh" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/staff/auth/refresh</code></b>
</p>
<p>
<label id="auth-GETapi-staff-auth-refresh" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-staff-auth-refresh" data-component="header"></label>
</p>
</form>


## Send a password reset email.




> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/staff/auth/password/reset" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","email":"johndoe@internet.com"}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/auth/password/reset"
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
    'https://loyalty.web/api/staff/auth/password/reset',
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


<div id="execution-results-POSTapi-staff-auth-password-reset" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-staff-auth-password-reset"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-staff-auth-password-reset"></code></pre>
</div>
<div id="execution-error-POSTapi-staff-auth-password-reset" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-staff-auth-password-reset"></code></pre>
</div>
<form id="form-POSTapi-staff-auth-password-reset" data-method="POST" data-path="api/staff/auth/password/reset" data-authed="0" data-hasfiles="0" data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-staff-auth-password-reset', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-staff-auth-password-reset" onclick="tryItOut('POSTapi-staff-auth-password-reset');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-staff-auth-password-reset" onclick="cancelTryOut('POSTapi-staff-auth-password-reset');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-staff-auth-password-reset" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/staff/auth/password/reset</code></b>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-staff-auth-password-reset" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>email</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="email" data-endpoint="POSTapi-staff-auth-password-reset" data-component="body" required  hidden>
<br>
User's email.
</p>

</form>


## Get user info.

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/staff/auth/user?uuid=praesentium" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/auth/user"
);

let params = {
    "uuid": "praesentium",
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
    'https://loyalty.web/api/staff/auth/user',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'query' => [
            'uuid'=> 'praesentium',
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
<div id="execution-results-GETapi-staff-auth-user" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-staff-auth-user"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-staff-auth-user"></code></pre>
</div>
<div id="execution-error-GETapi-staff-auth-user" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-staff-auth-user"></code></pre>
</div>
<form id="form-GETapi-staff-auth-user" data-method="GET" data-path="api/staff/auth/user" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-staff-auth-user', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-staff-auth-user" onclick="tryItOut('GETapi-staff-auth-user');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-staff-auth-user" onclick="cancelTryOut('GETapi-staff-auth-user');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-staff-auth-user" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/staff/auth/user</code></b>
</p>
<p>
<label id="auth-GETapi-staff-auth-user" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-staff-auth-user" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="GETapi-staff-auth-user" data-component="query" required  hidden>
<br>
uuid of website.
</p>
</form>


## Handle user logout.

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/staff/auth/logout" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/auth/logout"
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
    'https://loyalty.web/api/staff/auth/logout',
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


<div id="execution-results-POSTapi-staff-auth-logout" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-staff-auth-logout"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-staff-auth-logout"></code></pre>
</div>
<div id="execution-error-POSTapi-staff-auth-logout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-staff-auth-logout"></code></pre>
</div>
<form id="form-POSTapi-staff-auth-logout" data-method="POST" data-path="api/staff/auth/logout" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-staff-auth-logout', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-staff-auth-logout" onclick="tryItOut('POSTapi-staff-auth-logout');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-staff-auth-logout" onclick="cancelTryOut('POSTapi-staff-auth-logout');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-staff-auth-logout" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/staff/auth/logout</code></b>
</p>
<p>
<label id="auth-POSTapi-staff-auth-logout" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-staff-auth-logout" data-component="header"></label>
</p>
</form>


## Update profile.

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/staff/auth/profile" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"uuid":"283ca865-a71c-4d4a-b8cb-8c46c5b3ca57","name":"John Doe","email":"johndoe@internet.com","new_password":"password","current_password":"password"}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/auth/profile"
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
    'https://loyalty.web/api/staff/auth/profile',
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


<div id="execution-results-POSTapi-staff-auth-profile" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-staff-auth-profile"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-staff-auth-profile"></code></pre>
</div>
<div id="execution-error-POSTapi-staff-auth-profile" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-staff-auth-profile"></code></pre>
</div>
<form id="form-POSTapi-staff-auth-profile" data-method="POST" data-path="api/staff/auth/profile" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-staff-auth-profile', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-staff-auth-profile" onclick="tryItOut('POSTapi-staff-auth-profile');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-staff-auth-profile" onclick="cancelTryOut('POSTapi-staff-auth-profile');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-staff-auth-profile" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/staff/auth/profile</code></b>
</p>
<p>
<label id="auth-POSTapi-staff-auth-profile" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-staff-auth-profile" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="POSTapi-staff-auth-profile" data-component="body" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>name</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="name" data-endpoint="POSTapi-staff-auth-profile" data-component="body" required  hidden>
<br>
Full name of the user.
</p>
<p>
<b><code>email</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="email" data-endpoint="POSTapi-staff-auth-profile" data-component="body" required  hidden>
<br>
User's email.
</p>
<p>
<b><code>new_password</code></b>&nbsp;&nbsp;<small>string</small>     <i>optional</i> &nbsp;
<input type="password" name="new_password" data-endpoint="POSTapi-staff-auth-profile" data-component="body"  hidden>
<br>
New Password leave empty if you don't want to change the password.
</p>
<p>
<b><code>current_password</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="password" name="current_password" data-endpoint="POSTapi-staff-auth-profile" data-component="body" required  hidden>
<br>
Current Password.
</p>

</form>


## Request OTP for customer for any purpose

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/staff/auth/otp?uuid=283ca865-a71c-4d4a-b8cb-8c46c5b3ca57&purpose=id&mode=voluptate&customer=consequatur" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/auth/otp"
);

let params = {
    "uuid": "283ca865-a71c-4d4a-b8cb-8c46c5b3ca57",
    "purpose": "id",
    "mode": "voluptate",
    "customer": "consequatur",
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
    'https://loyalty.web/api/staff/auth/otp',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'query' => [
            'uuid'=> '283ca865-a71c-4d4a-b8cb-8c46c5b3ca57',
            'purpose'=> 'id',
            'mode'=> 'voluptate',
            'customer'=> 'consequatur',
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
<div id="execution-results-GETapi-staff-auth-otp" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-staff-auth-otp"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-staff-auth-otp"></code></pre>
</div>
<div id="execution-error-GETapi-staff-auth-otp" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-staff-auth-otp"></code></pre>
</div>
<form id="form-GETapi-staff-auth-otp" data-method="GET" data-path="api/staff/auth/otp" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-staff-auth-otp', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-staff-auth-otp" onclick="tryItOut('GETapi-staff-auth-otp');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-staff-auth-otp" onclick="cancelTryOut('GETapi-staff-auth-otp');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-staff-auth-otp" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/staff/auth/otp</code></b>
</p>
<p>
<label id="auth-GETapi-staff-auth-otp" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-staff-auth-otp" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="GETapi-staff-auth-otp" data-component="query" required  hidden>
<br>
uuid of website.
</p>
<p>
<b><code>purpose</code></b>&nbsp;&nbsp;<small>string</small>     <i>optional</i> &nbsp;
<input type="text" name="purpose" data-endpoint="GETapi-staff-auth-otp" data-component="query"  hidden>
<br>
required.
</p>
<p>
<b><code>mode</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="mode" data-endpoint="GETapi-staff-auth-otp" data-component="query" required  hidden>
<br>
number or card_number.
</p>
<p>
<b><code>customer</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="customer" data-endpoint="GETapi-staff-auth-otp" data-component="query" required  hidden>
<br>
customer number or customer card_number.
</p>
</form>


## Verify OTP code

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X POST \
    "https://loyalty.web/api/staff/auth/otp" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"code":"aut","purpose":"sit","mode":"enim","customer":"consequuntur"}'

```

```javascript
const url = new URL(
    "https://loyalty.web/api/staff/auth/otp"
);

let headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "code": "aut",
    "purpose": "sit",
    "mode": "enim",
    "customer": "consequuntur"
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
    'https://loyalty.web/api/staff/auth/otp',
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_AUTH_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'code' => 'aut',
            'purpose' => 'sit',
            'mode' => 'enim',
            'customer' => 'consequuntur',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


<div id="execution-results-POSTapi-staff-auth-otp" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-staff-auth-otp"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-staff-auth-otp"></code></pre>
</div>
<div id="execution-error-POSTapi-staff-auth-otp" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-staff-auth-otp"></code></pre>
</div>
<form id="form-POSTapi-staff-auth-otp" data-method="POST" data-path="api/staff/auth/otp" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-staff-auth-otp', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-POSTapi-staff-auth-otp" onclick="tryItOut('POSTapi-staff-auth-otp');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-POSTapi-staff-auth-otp" onclick="cancelTryOut('POSTapi-staff-auth-otp');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-POSTapi-staff-auth-otp" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/staff/auth/otp</code></b>
</p>
<p>
<label id="auth-POSTapi-staff-auth-otp" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="POSTapi-staff-auth-otp" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>code</code></b>&nbsp;&nbsp;<small>required</small>     <i>optional</i> &nbsp;
<input type="text" name="code" data-endpoint="POSTapi-staff-auth-otp" data-component="body"  hidden>
<br>
Otp Code.
</p>
<p>
<b><code>purpose</code></b>&nbsp;&nbsp;<small>required</small>     <i>optional</i> &nbsp;
<input type="text" name="purpose" data-endpoint="POSTapi-staff-auth-otp" data-component="body"  hidden>
<br>
string.
</p>
<p>
<b><code>mode</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="mode" data-endpoint="POSTapi-staff-auth-otp" data-component="body" required  hidden>
<br>
number or card_number.
</p>
<p>
<b><code>customer</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="customer" data-endpoint="POSTapi-staff-auth-otp" data-component="body" required  hidden>
<br>
customer number or customer card_number.
</p>

</form>



