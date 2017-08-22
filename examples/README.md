# How to run examples

First, clone repository.

Create `.env` file with linkedin credentials in the parent catalog (in the repository root) like this

```ini
LINKEDIN_CLIENT_ID=111ClinetId111
LINKEDIN_CLIENT_SECRET=222ClientSecret
```

To get client and secret go to [LinkedIn Developers portal](https://developer.linkedin.com/) and create new app there.

After add to OAuth 2.0 Authorized Redirect URLs:
```
http://localhost:8901/
```

Next, run PHP embedded server in the repository root:

```bash
php -S localhost:8901 -t examples
```

Navigate to http://localhost:8901/ 
