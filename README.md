# st2-wc-completions-builder
Builds the auto completions files for the ST2 WooCommerce plugin

**This plugin is not intended for public usage. It's written only for specific systems and WP build so please, do not use it if you do not know what you are doing.**

### Usage

* Open your project in ST2
* Open the functions files in `woocommerce/includes/` (do not open class files, only functions files
* Click `CTRL` + `SHIFT` + `F` and enable the regular expression search.
* Write `function` in the search field
* Write `<open-files>` in the path field
* Copy the search results and paste them in the `functions.txt` file
* Again search for `do_action` but in all the files in `woocommerce`
* Copy and paste in `actions.txt`
* Again search for `apply_filters` in all the files in `woocommerce`
* Copy and paste in `filters.txt`
* Activate the plugin in your WP installation
* Disable the plugin
* Check the files in the path `formatted_files/` inside this plugin and open the file `functions.sublime-completions`
* Be sure that the last function does not have a comma at the end of the line, if yes, remove it
* Do the same for the file `hooks.sublime-completions`