# How to run examples

First, [clone](https://help.github.com/articles/cloning-a-repository/) repository.

```bash
git clone https://github.com/zoonman/linkedin-api-php-client
```
Change dir to the repo 
```bash
cd linkedin-api-php-client
```

Install dependencies:

```bash
composer install [-d /path/to/repository/root]
```
If you don't have composer, you can get it [here](https://getcomposer.org/doc/00-intro.md).
Parameters in brackets are optional.

Create `.env` file with linkedin credentials in the parent catalog (in the repository root) like this

```ini
LINKEDIN_CLIENT_ID=111ClientId111
LINKEDIN_CLIENT_SECRET=222ClientSecret
```

The simplest way to do that to run the following commands:
```bash
echo 'LINKEDIN_CLIENT_ID=111ClientId111' >> .env
echo 'LINKEDIN_CLIENT_SECRET=222ClientSecret' >> .env
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

If you will see error like `Class 'Dotenv\Dotenv' not found...` install DotEnv using the following command:
```bash
composer require vlucas/phpdotenv
```
