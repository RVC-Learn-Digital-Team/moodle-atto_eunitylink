# Link Proxy #

Moodle Local plugin to return a hash to conceal url of links and
redirect link clicks to the actual URL. Depends on the atto
eunitylink button plugin for the calls to generate and return
the hash and create the link

Copy the eunitylink code to moodle\lib\editor\atto\plugins
Copy the linkproxy code to moodle\locl\linkproxy.

Login as admin and go to the admin menu. Go through the standard plugin install procedure.

After installation go to http://yourmoodle.org/admin/settings.php?section=editorsettingsatto
add a line in Toolbar config in the form
eUnity = eunitylink
Check in an instance of Atto that you have a new button indicated by a eUnity logo symbol for this plugin.

Confirm that the button icon is visible to teachers and not visible to students

Add a test link and confirm it performs the redirect and shows the image as expected.



## License ##

Copyright 2019 Titus Learning,  code by Marcus Green


This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.
