# FirebaseAuthenticationBundle
A lightweight, self-contained Symfony bundle providing authentication with JWTs generated from Firebase client SDK.

The only parameter you have to set is your Firebase project's ID in the `FIREBASE_PROJECT_ID` environment variable:

```env
# .env
FIREBASE_PROJECT_ID=your-project-id
```

Add the `firebase` authenticator to any of your app's firewall:

```yaml
# config/packages/security.yaml
security:
    firewalls:
        main:
            stateless: true
            firebase: ~
```
and authenticate your requests sending the JWT generated with the [Auth package of the Firebase JavaScript SDK](https://firebase.google.com/docs/reference/js/auth.md#auth_package) into an `Authorization: Bearer` HTTP header (accordingly to [the OAuth 2.0 specification](https://datatracker.ietf.org/doc/html/rfc6750#section-2.1)).

That's it! If the JWT is valid and **the email is verified**, the authenticated user will be identified using the `email` claim in the JWT payload.

Optionally, you can add a `leeway` (as a positive integer number of seconds) on a per-firewall basis to account for clock skew with Google's servers:

```yaml
# config/packages/security.yaml
security:
    firewalls:
        main:
            stateless: true
            firebase:
                leeway: 60
```
