## I2CE Customizations and hacks

### To use the Data Importer

#### Requirements
PHP 7, I2CE 4.3

#### Steps to import
* put data inside of manage/tools/data/*.csv
* cd to manage/tools (you must be in this dir)
* download the two files or you can git glone which makes the previous step unnecessary
* run ```php import_facilities.php data/csvfilename.csv```
* Do these look like the correct first row? Enter y [Enter]
* Is this is test run: Enter n [Enter]
* Skip rows? Enter n [Enter]
* For each of the next questions Type in a [Enter].
* After a two or three a, grab a piece of cake and wait for it to finish.
* You can go to your iHRIS installation and visit Administer Database and look for Regions, Districts and Facilities.