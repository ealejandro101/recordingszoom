The following steps should get you up and running with
this module template code.

* DO NOT PANIC!

* Unzip the archive and read this file

* Rename the recordingszoom/ folder to the name of your module (eg "widget").
  The module folder MUST be lower case and can't contain underscores. You should check the CVS contrib
  area at http://cvs.moodle.org/contrib/plugins/mod/ to make sure that
  your name is not already used by an other module. Registering the plugin
  name @ http://moodle.org/plugins will secure it for you.

* Edit all the files in this directory and its subdirectories and change
  all the instances of the string "recordingszoom" to your module name
  (eg "widget"). If you are using Linux, you can use the following command
  $ find . -type f -exec sed -i 's/recordingszoom/widget/g' {} \;
  $ find . -type f -exec sed -i 's/recordingszoom/WIDGET/g' {} \;

  On a mac, use:
  $ find . -type f -exec sed -i '' 's/recordingszoom/widget/g' {} \;
  $ find . -type f -exec sed -i '' 's/recordingszoom/WIDGET/g' {} \;

* Rename the file lang/en/recordingszoom.php to lang/en/widget.php
  where "widget" is the name of your module

* Rename all files in backup/moodle2/ folder by replacing "recordingszoom" with
  the name of your module

  On Linux you can perform this and previous steps by calling:
  $ find . -depth -name '*recordingszoom*' -execdir bash -c 'mv -i "$1" "${1//recordingszoom/widget}"' bash {} \;

* Place the widget folder into the /mod folder of the moodle
  directory.

* Modify version.php and set the initial version of you module.

* Visit Settings > Site Administration > Notifications, you should find
  the module's tables successfully created

* Go to Site Administration > Plugins > Activity modules > Manage activities
  and you should find that this recordingszoom has been added to the list of
  installed modules.

* You may now proceed to run your own code in an attempt to develop
  your module. You will probably want to modify mod_form.php and view.php
  as a first step. Check db/access.php to add capabilities.
  Go to Settings > Site Administration > Development > XMLDB editor
  and modify the module's tables.

We encourage you to share your code and experience - visit http://moodle.org

Good luck!
