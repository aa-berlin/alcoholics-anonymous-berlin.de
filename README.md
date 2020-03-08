# alcoholics-anonymous-berlin.de

This is a development setup for `alcoholics-anonymous-berlin.de`.

It contains the site's theme including customizations of the `12-step-meeting-list` plugin.

The theme is based on [CrestaProject's Zenzero](./wp-content/themes/zenzero/readme.txt)

## Installation

Do not forget to merge these into your respctive local files:

* [.sample.htaccess](./.sample.htaccess) into [.htaccess](./.htaccess)
* [wp-config-sample.php](./wp-config-sample.php) into [wp-config.php](./wp-config.php)

## Cron-Job

The [sample config](./wp-config-sample.php) in this repo disables the implicit handling of cron jobs by WordPress during requests.

To run cron tasks, set up a cron job by calling he following URL:

```
https://<your-website>/wp-cron.php?doing_wp_cron&cron_key=<your-cron-key>
```

Where `<your-website>` might just be `www.alcoholics-anonymous-berlin.de` and `<your-cron-key>` must match the pattern in your top-level [.htaccess](./.htaccess).
