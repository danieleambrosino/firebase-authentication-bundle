# FirebaseAuthenticationBundle
A lightweight Symfony bundle providing authentication for JWTs generated from Firebase client SDK.

The only parameter you have to set is your Firebase project's ID in the `FIREBASE_PROJECT_ID` environment variable:

```env
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
And that's it!

Optionally, you can add a `leeway` to account for clock skew with Google's servers:

```yaml
# config/packages/security.yaml
security:
	firewalls:
		main:
			stateless: true
			firebase:
				leeway: 60
```
