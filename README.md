# FirebaseAuthenticationBundle
A lightweight Symfony bundle providing authentication through Firebase JWT.

No configuration is required, it just works out of the box!

Authenticate your requests sending a JWT into an `Authorization: Bearer` HTTP header.

To enable the authenticator, add the `firebase` authenticator to any of your app's firewall:

```yaml
# config/packages/security.yaml
security:
	firewalls:
		main:
			stateless: true
			firebase: ~
```
and that's it!

Optionally, you can add a `leeway` to deal with clock skew with Google's servers:

```yaml
# config/packages/security.yaml
security:
	firewalls:
		main:
			stateless: true
			firebase:
				leeway: 60
```
