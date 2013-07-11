# Setup

1. Prepare yourself. This is a plugin for **test** sites. When you run a reset, all your files and your database will be reset to your last Git commit. This will cause data loss!! **DO NOT** use it on sites containing critical or sensitive data!!!
1. Install the plugin in WordPress
1. Setup Git version control for your WordPress install. For example, `git init; git add .; git commit -m "Initial commit";`
1. Set up your WordPress install however you like, then run `Reset > Update Defaults` in the admin bar.
1. Verify your server allows access to PHP `exec()`. One good test is that if the plugin doesn't work, your server doesn't allow it. ;)
1. Add this to your .htaccess file:

    # Deny public access to .sql files.
    # Claim File Not Found instead of "Forbidden" so file existance isn't revealed
    <Files ~ "\.sql$">
    	Order Deny,Allow
    	Allow from all
    	Satisfy All
    	Redirect 404 /
    </Files>

# Usage

Any time you want to reset WordPress back to your default files and settings, select `Reset > Reset WordPress` in the Admin bar.

Any time you want to update to new defaults (make a Git commit and database dump), select `Reset > Update Defaults` in the Admin bar.

Undo the last Git commit, but keep file changes (command line):

`git reset --soft HEAD^`