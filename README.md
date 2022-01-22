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
and authenticate your requests sending a JWT into an `Authorization: Bearer` HTTP header.
That's it! If the JWT is valid, the authenticated user will be provided using the `email` claim in the JWT payload.

Optionally, you can add a `leeway` (as a positive integer number of seconds) to account for clock skew with Google's servers:

```yaml
# config/packages/security.yaml
security:
    firewalls:
        main:
            stateless: true
            firebase:
                leeway: 60
```
