# Global

This api can be reached using staff or customer token

## Get available stores

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/campaign/stores?uuid=283ca865-a71c-4d4a-b8cb-8c46c5b3ca57" \
    -H "Authorization: Bearer {YOUR_AUTH_KEY}" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/campaign/stores"
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
    'https://loyalty.web/api/campaign/stores',
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
<div id="execution-results-GETapi-campaign-stores" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-campaign-stores"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-campaign-stores"></code></pre>
</div>
<div id="execution-error-GETapi-campaign-stores" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-campaign-stores"></code></pre>
</div>
<form id="form-GETapi-campaign-stores" data-method="GET" data-path="api/campaign/stores" data-authed="1" data-hasfiles="0" data-headers='{"Authorization":"Bearer {YOUR_AUTH_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-campaign-stores', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-campaign-stores" onclick="tryItOut('GETapi-campaign-stores');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-campaign-stores" onclick="cancelTryOut('GETapi-campaign-stores');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-campaign-stores" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/campaign/stores</code></b>
</p>
<p>
<label id="auth-GETapi-campaign-stores" hidden>Authorization header: <b><code>Bearer </code></b><input type="text" name="Authorization" data-prefix="Bearer " data-endpoint="GETapi-campaign-stores" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="GETapi-campaign-stores" data-component="query" required  hidden>
<br>
uuid of website.
</p>
</form>


## Get list of industry




> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/industry" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/industry"
);

let headers = {
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
    'https://loyalty.web/api/industry',
    [
        'headers' => [
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
    "data": [
        {
            "id": 1,
            "uuid": "13889b27-2bd9-461a-9644-40b6f96ee5d8",
            "name": "Accounting & Legal"
        },
        {
            "id": 2,
            "uuid": "6ea9ccaa-5cce-4077-a26b-79a6cc8d7a1a",
            "name": "Advertising"
        },
        {
            "id": 3,
            "uuid": "e37cccd6-b90c-42ac-b03f-edb99ad271f7",
            "name": "Aerospace"
        },
        {
            "id": 4,
            "uuid": "b52ad749-3c25-4c6f-a61a-8da46aab3277",
            "name": "Agriculture"
        },
        {
            "id": 5,
            "uuid": "2959ed2d-a65c-4e1e-9ffb-ec4a8ac7f19c",
            "name": "Automotive"
        },
        {
            "id": 6,
            "uuid": "89234ae5-9a69-48a7-88e6-17b42415b7a4",
            "name": "Banking & Finance"
        },
        {
            "id": 7,
            "uuid": "b3e524d2-44a6-4f1c-a595-42acb256da3c",
            "name": "Bars & Nightclubs"
        },
        {
            "id": 8,
            "uuid": "7450c951-1db3-4cd5-913f-9a9f09b8651a",
            "name": "Biotechnology"
        },
        {
            "id": 9,
            "uuid": "a34c030f-c492-474d-b477-154143aed1a7",
            "name": "Broadcasting"
        },
        {
            "id": 10,
            "uuid": "67744ad0-0d8d-4ea7-8ca1-e046366a71a1",
            "name": "Business Services"
        },
        {
            "id": 11,
            "uuid": "5062f195-5984-4a69-b641-1dfcf2e82679",
            "name": "Commodities & Chemicals"
        },
        {
            "id": 12,
            "uuid": "7ffecf7e-4603-46b5-808f-cc052a1f2fcd",
            "name": "Communications"
        },
        {
            "id": 13,
            "uuid": "826c5c03-6cca-4b2b-b538-78c241d39dd5",
            "name": "Computers & Hightech"
        },
        {
            "id": 14,
            "uuid": "93e51de7-500e-471d-8372-ab849f470a31",
            "name": "Construction"
        },
        {
            "id": 15,
            "uuid": "a9494717-0bc6-493b-8347-0aa9ccdbc2e6",
            "name": "Defense"
        },
        {
            "id": 16,
            "uuid": "3c4a8319-9c68-4673-963a-755b1069f673",
            "name": "Energy"
        },
        {
            "id": 17,
            "uuid": "1b19c3c1-ff0a-43c7-b806-2e5f2ce79fb9",
            "name": "Entertainment"
        },
        {
            "id": 18,
            "uuid": "1b6a374d-8d77-4432-b5fe-60bd4e6dc457",
            "name": "Government"
        },
        {
            "id": 19,
            "uuid": "fe6ca188-689a-4ddc-bc0f-ea191905482d",
            "name": "Healthcare & Life Sciences"
        },
        {
            "id": 20,
            "uuid": "94ffd9ef-c387-4c7f-9942-01a018c218bd",
            "name": "Insurance"
        },
        {
            "id": 21,
            "uuid": "4b518164-b89d-4447-b558-bd158515e876",
            "name": "Internet & Online"
        },
        {
            "id": 22,
            "uuid": "07efd312-9aae-4272-ba50-df4a061127a4",
            "name": "Manufacturing"
        },
        {
            "id": 23,
            "uuid": "dcdb33bd-4199-46c4-a70d-641986f70cea",
            "name": "Marketing"
        },
        {
            "id": 24,
            "uuid": "9638c9ff-177d-4060-af11-9321f50a5f50",
            "name": "Media"
        },
        {
            "id": 25,
            "uuid": "758201fa-1c28-4c45-aead-0a102b5117a2",
            "name": "Nonprofit & Higher Education"
        },
        {
            "id": 26,
            "uuid": "41676604-55a1-47fb-b6d3-4d5f878ebaf9",
            "name": "Other"
        },
        {
            "id": 27,
            "uuid": "a667ea6b-e51b-4a7f-b13e-093252647757",
            "name": "Pharmaceuticals"
        },
        {
            "id": 28,
            "uuid": "a859941f-a48c-4c78-a73a-b5b6937e1b83",
            "name": "Photography"
        },
        {
            "id": 29,
            "uuid": "fe257b9a-80f8-4d7c-899b-1f8e59a650fc",
            "name": "Professional Services & Consulting"
        },
        {
            "id": 30,
            "uuid": "06b91266-c8a7-4e1d-b3ab-3e4eaedfa8ef",
            "name": "Real Estate"
        },
        {
            "id": 31,
            "uuid": "17e4c303-420d-405d-b9e4-d934709a2e35",
            "name": "Restaurant & Catering"
        },
        {
            "id": 32,
            "uuid": "1ac5173e-cda5-4592-b49b-a542a3468de8",
            "name": "Retail & Wholesale"
        },
        {
            "id": 33,
            "uuid": "3852d1d0-128f-4871-a905-8f1968e6d280",
            "name": "Software & Development"
        },
        {
            "id": 34,
            "uuid": "b5389e41-76db-48d6-811f-e7f90320380e",
            "name": "Sports"
        },
        {
            "id": 35,
            "uuid": "7e429fa3-5da4-44fe-baff-ad5b7917dfb2",
            "name": "Transportation"
        },
        {
            "id": 36,
            "uuid": "06e936d9-10a2-4433-8df3-2a18402f377e",
            "name": "Travel & Luxury"
        }
    ]
}
```
<div id="execution-results-GETapi-industry" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-industry"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-industry"></code></pre>
</div>
<div id="execution-error-GETapi-industry" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-industry"></code></pre>
</div>
<form id="form-GETapi-industry" data-method="GET" data-path="api/industry" data-authed="0" data-hasfiles="0" data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-industry', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-industry" onclick="tryItOut('GETapi-industry');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-industry" onclick="cancelTryOut('GETapi-industry');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-industry" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/industry</code></b>
</p>
</form>


## Get list of website by selected industry




> Example request:

```bash
curl -X GET \
    -G "https://loyalty.web/api/industry/blanditiis" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://loyalty.web/api/industry/blanditiis"
);

let headers = {
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
    'https://loyalty.web/api/industry/blanditiis',
    [
        'headers' => [
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


> Example response (500):

```json
{
    "message": "Attempt to read property \"users\" on null",
    "exception": "ErrorException",
    "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/app\/Platform\/Controllers\/GlobalApi\/IndustryController.php",
    "line": 41,
    "trace": [
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/app\/Platform\/Controllers\/GlobalApi\/IndustryController.php",
            "line": 41,
            "function": "handleError",
            "class": "Illuminate\\Foundation\\Bootstrap\\HandleExceptions",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Controller.php",
            "line": 54,
            "function": "website",
            "class": "Platform\\Controllers\\GlobalApi\\IndustryController",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/ControllerDispatcher.php",
            "line": 45,
            "function": "callAction",
            "class": "Illuminate\\Routing\\Controller",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Route.php",
            "line": 254,
            "function": "dispatch",
            "class": "Illuminate\\Routing\\ControllerDispatcher",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Route.php",
            "line": 197,
            "function": "runController",
            "class": "Illuminate\\Routing\\Route",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Router.php",
            "line": 695,
            "function": "run",
            "class": "Illuminate\\Routing\\Route",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php",
            "line": 128,
            "function": "Illuminate\\Routing\\{closure}",
            "class": "Illuminate\\Routing\\Router",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Middleware\/SubstituteBindings.php",
            "line": 50,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php",
            "line": 167,
            "function": "handle",
            "class": "Illuminate\\Routing\\Middleware\\SubstituteBindings",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Middleware\/ThrottleRequests.php",
            "line": 127,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Middleware\/ThrottleRequests.php",
            "line": 63,
            "function": "handleRequest",
            "class": "Illuminate\\Routing\\Middleware\\ThrottleRequests",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php",
            "line": 167,
            "function": "handle",
            "class": "Illuminate\\Routing\\Middleware\\ThrottleRequests",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php",
            "line": 103,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Router.php",
            "line": 697,
            "function": "then",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Router.php",
            "line": 672,
            "function": "runRouteWithinStack",
            "class": "Illuminate\\Routing\\Router",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Router.php",
            "line": 636,
            "function": "runRoute",
            "class": "Illuminate\\Routing\\Router",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Router.php",
            "line": 625,
            "function": "dispatchToRoute",
            "class": "Illuminate\\Routing\\Router",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Kernel.php",
            "line": 166,
            "function": "dispatch",
            "class": "Illuminate\\Routing\\Router",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php",
            "line": 128,
            "function": "Illuminate\\Foundation\\Http\\{closure}",
            "class": "Illuminate\\Foundation\\Http\\Kernel",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/fruitcake\/laravel-cors\/src\/HandleCors.php",
            "line": 38,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php",
            "line": 167,
            "function": "handle",
            "class": "Fruitcake\\Cors\\HandleCors",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/fideloper\/proxy\/src\/TrustProxies.php",
            "line": 57,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php",
            "line": 167,
            "function": "handle",
            "class": "Fideloper\\Proxy\\TrustProxies",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Middleware\/TransformsRequest.php",
            "line": 21,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Middleware\/ConvertEmptyStringsToNull.php",
            "line": 31,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php",
            "line": 167,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Http\\Middleware\\ConvertEmptyStringsToNull",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Middleware\/TransformsRequest.php",
            "line": 21,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Middleware\/TrimStrings.php",
            "line": 40,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php",
            "line": 167,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Http\\Middleware\\TrimStrings",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Middleware\/ValidatePostSize.php",
            "line": 27,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php",
            "line": 167,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Http\\Middleware\\ValidatePostSize",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Middleware\/PreventRequestsDuringMaintenance.php",
            "line": 86,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php",
            "line": 167,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Http\\Middleware\\PreventRequestsDuringMaintenance",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/app\/Http\/Middleware\/CheckInstallation.php",
            "line": 26,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php",
            "line": 167,
            "function": "handle",
            "class": "App\\Http\\Middleware\\CheckInstallation",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Pipeline\/Pipeline.php",
            "line": 103,
            "function": "Illuminate\\Pipeline\\{closure}",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Kernel.php",
            "line": 141,
            "function": "then",
            "class": "Illuminate\\Pipeline\\Pipeline",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Http\/Kernel.php",
            "line": 110,
            "function": "sendRequestThroughRouter",
            "class": "Illuminate\\Foundation\\Http\\Kernel",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/knuckleswtf\/scribe\/src\/Extracting\/Strategies\/Responses\/ResponseCalls.php",
            "line": 324,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Http\\Kernel",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/knuckleswtf\/scribe\/src\/Extracting\/Strategies\/Responses\/ResponseCalls.php",
            "line": 305,
            "function": "callLaravelOrLumenRoute",
            "class": "Knuckles\\Scribe\\Extracting\\Strategies\\Responses\\ResponseCalls",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/knuckleswtf\/scribe\/src\/Extracting\/Strategies\/Responses\/ResponseCalls.php",
            "line": 76,
            "function": "makeApiCall",
            "class": "Knuckles\\Scribe\\Extracting\\Strategies\\Responses\\ResponseCalls",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/knuckleswtf\/scribe\/src\/Extracting\/Strategies\/Responses\/ResponseCalls.php",
            "line": 51,
            "function": "makeResponseCall",
            "class": "Knuckles\\Scribe\\Extracting\\Strategies\\Responses\\ResponseCalls",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/knuckleswtf\/scribe\/src\/Extracting\/Strategies\/Responses\/ResponseCalls.php",
            "line": 41,
            "function": "makeResponseCallIfEnabledAndNoSuccessResponses",
            "class": "Knuckles\\Scribe\\Extracting\\Strategies\\Responses\\ResponseCalls",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/knuckleswtf\/scribe\/src\/Extracting\/Generator.php",
            "line": 236,
            "function": "__invoke",
            "class": "Knuckles\\Scribe\\Extracting\\Strategies\\Responses\\ResponseCalls",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/knuckleswtf\/scribe\/src\/Extracting\/Generator.php",
            "line": 172,
            "function": "iterateThroughStrategies",
            "class": "Knuckles\\Scribe\\Extracting\\Generator",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/knuckleswtf\/scribe\/src\/Extracting\/Generator.php",
            "line": 127,
            "function": "fetchResponses",
            "class": "Knuckles\\Scribe\\Extracting\\Generator",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/knuckleswtf\/scribe\/src\/Commands\/GenerateDocumentation.php",
            "line": 119,
            "function": "processRoute",
            "class": "Knuckles\\Scribe\\Extracting\\Generator",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/knuckleswtf\/scribe\/src\/Commands\/GenerateDocumentation.php",
            "line": 73,
            "function": "processRoutes",
            "class": "Knuckles\\Scribe\\Commands\\GenerateDocumentation",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Container\/BoundMethod.php",
            "line": 36,
            "function": "handle",
            "class": "Knuckles\\Scribe\\Commands\\GenerateDocumentation",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Container\/Util.php",
            "line": 40,
            "function": "Illuminate\\Container\\{closure}",
            "class": "Illuminate\\Container\\BoundMethod",
            "type": "::"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Container\/BoundMethod.php",
            "line": 93,
            "function": "unwrapIfClosure",
            "class": "Illuminate\\Container\\Util",
            "type": "::"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Container\/BoundMethod.php",
            "line": 37,
            "function": "callBoundMethod",
            "class": "Illuminate\\Container\\BoundMethod",
            "type": "::"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Container\/Container.php",
            "line": 651,
            "function": "call",
            "class": "Illuminate\\Container\\BoundMethod",
            "type": "::"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Console\/Command.php",
            "line": 136,
            "function": "call",
            "class": "Illuminate\\Container\\Container",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/symfony\/console\/Command\/Command.php",
            "line": 299,
            "function": "execute",
            "class": "Illuminate\\Console\\Command",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Console\/Command.php",
            "line": 121,
            "function": "run",
            "class": "Symfony\\Component\\Console\\Command\\Command",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/symfony\/console\/Application.php",
            "line": 978,
            "function": "run",
            "class": "Illuminate\\Console\\Command",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/symfony\/console\/Application.php",
            "line": 295,
            "function": "doRunCommand",
            "class": "Symfony\\Component\\Console\\Application",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/symfony\/console\/Application.php",
            "line": 167,
            "function": "doRun",
            "class": "Symfony\\Component\\Console\\Application",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Console\/Application.php",
            "line": 92,
            "function": "run",
            "class": "Symfony\\Component\\Console\\Application",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Console\/Kernel.php",
            "line": 129,
            "function": "run",
            "class": "Illuminate\\Console\\Application",
            "type": "->"
        },
        {
            "file": "\/Users\/qudratnurfajarys\/Projects\/loyalty.web\/artisan",
            "line": 37,
            "function": "handle",
            "class": "Illuminate\\Foundation\\Console\\Kernel",
            "type": "->"
        }
    ]
}
```
<div id="execution-results-GETapi-industry--id-" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-industry--id-"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-industry--id-"></code></pre>
</div>
<div id="execution-error-GETapi-industry--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-industry--id-"></code></pre>
</div>
<form id="form-GETapi-industry--id-" data-method="GET" data-path="api/industry/{id}" data-authed="0" data-hasfiles="0" data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-industry--id-', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
        <button type="button" style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-tryout-GETapi-industry--id-" onclick="tryItOut('GETapi-industry--id-');">Try it out ⚡</button>
    <button type="button" style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-canceltryout-GETapi-industry--id-" onclick="cancelTryOut('GETapi-industry--id-');" hidden>Cancel</button>&nbsp;&nbsp;
    <button type="submit" style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;" id="btn-executetryout-GETapi-industry--id-" hidden>Send Request 💥</button>
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/industry/{id}</code></b>
</p>
<h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
<p>
<b><code>id</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="id" data-endpoint="GETapi-industry--id-" data-component="url" required  hidden>
<br>
id of industry
</p>
</form>



