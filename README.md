Football Club Exporter
=====================

Export [Football Club](http://themeboy.com/footballclub/) data to CSV files. Useful for importing into [SportsPress](https://themeboy.com/sportspress/).

## Exporting from Football Club
1. Download the ZIP file.
2. Uncompress into wp-content/plugins or go to your WordPress dashboard > Plugins > Add New > Upload and select the ZIP file.
3. Once installed, activate the plugin.
4. Go to Tools > Export Football Club.
5. Click each button to export clubs, players, staff, matches, and sponsors. CSV files will be downloaded.

## Importing to SportsPress
1. Go to the dashboard of a WordPress installation with SportsPress installed, or get it from the WP repository.
2. From Tools > Import, click on any of the SportsPress CSV import tools.
3. Upload the corresponding file, leave Delimiter as default (,) and click "Upload file and import".

## Troubleshooting
The events importer can be particularly tricky.
* Be sure that the player performance columns in SportsPress events matches the columns to the right of the "Players" column in the CSV file. Default columns are: Goals, Assists, Yellow Cards, and Red Cards.
* If your events data spans across multiple league and/or seasons, you may wish to split the CSV file before importing. When saving the CSV files, be sure to use the date format yyyy-mm-dd.