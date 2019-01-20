# What are Cloud Hooks?

For a comprehensive overview of the Cloud Hooks feature, please refer to the canonical source at https://github.com/acquia/cloud-hooks.

# What value does this project add?

This project was started in response to a number of pain-points that were encountered with managing enterprise Drupal 8 projects on Acquia Cloud.

It is comprised of a number of hooks that fire in a specific order to help make code deployments faster, more predictable, and to prevent human error.

It was also created because of the lack of ACAPI support for Drush 9.

## Prerequisites
This project makes certain assumptions about application and organization setup.

### Cloudflare Support Tier
Domain based purging requires an enterprise Cloudflare account.

### Modules
- Drush >= 9.2.1
- Cloudflare >= 8.x-1.0-alpha7

### Environment Setup
Before deploying these hooks to an Acquia application, certain steps must be taken to install the proper Acquia and Cloudflare credentials.

#### Setting up ACAPI
To set up ACAPI on an environment, credentials must be installed on the Acquia server(s) being targeted.

This can be accomplished by:
 - running the `ac-api-login` command with Drush 8.
 - Uploading a credentials file at `~/.acquia/cloudapi.conf`.
 
```json
{"email":"account@example.com", "key":"get-the-key-from-the-acquia-dashboard"}
```
#### Configuring Cloudflare API
The Cloudflare API keys need to be configured within the Drupal database at `/admin/config/services/cloudflare`.

## Scripts
Each operation is separated into its own script.  These scripts are shared among the various environments through carefully named symbolic links.

### Backup Databases
This script will auto-detect all schemas on the current environment and create user backups of each before exiting.

This script utilizes v1 of the Acquia Cloud REST API to block subsequent hook execution until all backups are completed.  By default, it only runs on the production environment.

### Drupal Cache Clear
This script clears the local Drupal cache.  It adds a layer of safety and consistency by also clearing the Drush cache prior to clearing everything else.  This is also said to resolve https://github.com/acquia/blt/issues/2867.

### Update Entities
This script simply runs `drush entity:updates`.

### Update Databases
This script simply runs `drush updatedb`.

### Configuration Import
This script runs a configuration import against the `sync` source.  If a configuration split exists for the current environment, then the environment-specific configuration is imported as well.

### Varnish Cache Clear
This script will auto-detect all domains on the current environment and purge the Varnish cache for each before exiting.

It utilizes the Acquia Cloud REST APIv1 to perform purge requests and will block subsequent hook execution until all caches are purged (to prevent race conditions with clearing upstream caching layers).

### Cloudflare Cache Clear
This script will auto-detect all domains on the current environment and purge the Cloudflare cache for each before exiting.

It utilizes the Cloudflare REST APIv4 to perform purge requests and will **NOT** block subsequent hook execution, as this is not supported through the API at this time.