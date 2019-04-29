# linkst.rip
A URL shortening service built with a Symfony based backend, and a React frontend. https://linkst.rip

# Available API Endpoints
### /api/checkUrl - Check if a url is valid
### /api/checkSlug - Check if a slug is valid/available
### /api/newLink - Create a new linkst.rip link

# Check if you have a valid URL
Send a URL in a GET request to the checkUrl endpoint like this:
```
https://linkst.rip/api/checkUrl?url=http://some.url/
```
A JSON response will be returned with a 'success' key. If it is false, an 'error' key will be included with a message, detailing why.

# Check if your slug is valid/available
Send a slug in a GET request to the checkSlug endpoint like this:
```
https://linkst.rip/api/checkSlug?slug=testSlug
```
A JSON response will be returned with a 'success' key. It will only be true if the provided slug is both valid and available. If not, an 'error' key will be available

If no value is provided for slug, JSON will always return a 'success' key of true, since this means a random slug will be generated.

# Create new linkst.rip URL
Send a URL and optional slug in a GET request to the newLink endpoint like this:
```
https://linkst.rip/api/newLink?url=http://some.url/&slug=testSlug
```
If no slug value is given, a random string will be used for link creation.

If the link creation was not successful, there will be an 'error' key in the JSON with an explanation.

If the link was created successfully, the JSON will contain a 'url' key with the linkst.rip link.
