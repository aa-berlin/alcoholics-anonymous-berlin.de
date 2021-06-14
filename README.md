# alcoholics-anonymous-berlin.de

This is a development setup for `alcoholics-anonymous-berlin.de`.

It contains the site's theme including customizations of the `12-step-meeting-list` plugin.

The theme is based on [CrestaProject's Zenzero](https://crestaproject.com/downloads/zenzero/)

## Cloning

Be sure to initialize sub-modules:

```bash
$ git submodule init
$ git submodule update
$ cd wp-content/plugins/12-step-meeting-list
$ git remote add upstream git@github.com:code4recovery/12-step-meeting-list.git
$ git fetch --all
 ```

## Installation

Prepare an external `mysql` instance.

Download and unzip a WordPress installation archive in the project root.

Do not forget to customize and merge these into your respective local files:

* [.sample.htaccess](./.sample.htaccess) into [.htaccess](./.htaccess)
* [wp-config-sample.php](./wp-config-sample.php) into [wp-config-app-1.php](./wp-config-app-1.php) and [wp-config-app-2.php](./wp-config-app-2.php) respectively.

### Cron-Job

The [sample config](./wp-config-sample.php) in this repo disables the implicit handling of cron jobs by WordPress during requests.

To run cron tasks, set up a cron job by calling he following URL:

```
https://<your-website>/wp-cron.php?doing_wp_cron&cron_key=<your-cron-key>
```

The command in full using wget:

```
wget -q -O - 'https://<your-website>/wp-cron.php?doing_wp_cron&cron_key=<your-cron-key>' >/dev/null 2>&1
```

Where `<your-website>` might just be `www.alcoholics-anonymous-berlin.de` and `<your-cron-key>` must match the pattern in your top-level [.htaccess](./.htaccess).

### Optional plugins

We are currently using Wordpress for updating and installing plugins via its GUI.

These plugins might be of interest to you, or are installed on live already:

* 12 Step Meeting List
* Disable Comments
* Flo Forms
* Members
* Redirection
* WP Crontrol

## Deployment

Automatic deployment to the live site via FTPS can be done via the `bin/deploy` script:

```shell script
$ bin/deploy PROD
```

Configure the connection details in [`.env`](./.env).

Currently only the following folders will be copied, though:

* [`wp-content/themes/zenzero`](wp-content/themes/zenzero)
* [`wp-content/themes/zenzero-aa`](wp-content/themes/zenzero-aa)
* [`wp-content/plugins/aa-berlin-addons`](wp-content/plugins/aa-berlin-addons)

## Misc

### Bumping to a new version and forcing an asset refresh

Make sure to only do this step on a clean working copy, everything having been committed, as it will edit source files in place!

Theme and plugin (`zenzero-aa` and `aa-berlin-addons`) might need a refresh of cached resources in the browser, or generally deserve a new version.

You can and should update them both with the `bin/bump-version` script:

```shell script
$ bin/bump-version 1.3.5
```

### Adding filter settings to the Recent Posts Widget

* Add to widget title (will be removed before rendering)
* `[aa-berlin-filter-<setting>=<value>]`
* Neither `<setting>` nor `<value>` may contain a bracket ("`]`")
* Example for `<setting>`: `category_name`
* See [`wp-includes/widgets/class-wp-widget-recent-posts.php:73`](./wp-includes/widgets/class-wp-widget-recent-posts.php) for the filter being used
* See [`wp-includes/class-wp-query.php:3463`](./wp-includes/class-wp-query.php) query-vars available

### SQL to create meeting url entries from post content

```sql
INSERT INTO wp_postmeta(meta_key, meta_value, post_id) SELECT 'conference_url', REGEXP_SUBSTR(p.post_content, 'https:\\S+zoom\\S+') AS url, p.ID FROM wp_posts p WHERE p.post_type = 'tsml_meeting' AND p.post_content REGEXP 'https:\\S+zoom\\S+';
```

## License

This code is available under the terms of the GNU General Public License, Version 3 or later: https://www.gnu.org/licenses/gpl-3.0.html

Copyright 2021 alcoholics-anonymous-berlin.de
