// https://app.spera.io/api/account   GET request returns this
// this logs in an account and username
var restJson = {
  "id": "/api/account",
  "links": [
      {"image": "damon.jpeg", "title": "Damon Hogan"}, //might provide some profile picture without auth
      {"video": "damon.mov", "title": "Damon Hogan"}, //might provide some video link without auth
      {"get_oauth2_token": "https://...", "title": "Get Oauth2 Token"}
  ],
  "operations": [{
      "rel": "active",
      "method": "PUT",
      "href": "/api/account",
      "expects": {
          "state": "active",
          "token": "someoauth2token"
      }

  }]
};

// https://app.spera.io/api/account?accountName=damon&username=damon   PUT the json
//this gets a token for login and other operations
var restJson = {
    "id": "/api/token",
    "links": [
        /*
        not sure on links for token yet
        {"image": "damon.jpeg", "title": "Damon Hogan"}, //might provide some profile picture without auth

        {"video": "damon.mov", "title": "Damon Hogan"}, //might provide some video link without auth
        {"get_oauth2_token": "https://...", "title": "Get Oauth2 Token"} */
    ],
    "operations": [{
        "rel": "active",
        "method": "PUT",
        "href": "/api/token",
        "expects": {
            "state": "active",
            "account": "damon",
            "username": "damon" /*,
            "token": "someoauth2token"*/
        }

    }]
};


