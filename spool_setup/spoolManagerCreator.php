<?php
	require_once(__DIR__."/../php/config/Config.php");

	//Create spoolManager file
	$convertableExts = array_diff(Config::$extensions, Config::$unconvertableExts);
	$supportedExtsRegex = '\.'.implode('$\|\.',array_keys($convertableExts) ).'$'; //formating regex

	$spoolDirPath = Config::getSpoolDirPath();
	$spoolSetupDirPath = Config::getSpoolSetupDirPath();
	$fileStoragePath = Config::getFileStoragePath();
	$PIDFileName = 'spoolManagerPID.txt';

	$shellContent = 
"#!/bin/sh

# This shell script is a polling script that would convert any supported file present inside the spool directory into pdf.
# It is an alternative solution to executing libreoffice as www-data. What this script will do is, it will infinitely loop
# and execute any file that is present with the supported extension, then spit the output to the storage directory; while 
# also moving the original file to the storage directory.

# NOTE: This script must be recreated from spoolManangerCreator.php everytime an extension support list is updated in Config.php

#Check if spoolManagerPID.txt exist and has size > 0
if [ -s $spoolSetupDirPath$PIDFileName ]; then
	#kill the previous process if pid exist in user process space
	PID=`cat $spoolSetupDirPath$PIDFileName`
	if [ `ps -a | grep -c \$PID` -gt 0 ]; then 
		kill \$PID #kill process
	fi
fi


#save the current process id to spoolmanagerPID.txt
echo \$\$ > $PIDFileName

#Infinite loop, polling
while true 
do 
	# Check for files with supported extensions
	if [ `ls $spoolDirPath | grep -c '$supportedExtsRegex'` -gt 0 ]; then
		# Get those files and loop thru
		ls -dR $spoolDirPath* | grep '$supportedExtsRegex' | while read file; do
			#convert file and store output in storage
			libreoffice --headless --convert-to pdf --outdir $fileStoragePath \$file 
			#move original file to storage
			if [ $? = 0 ]; then
				mv \"\$file\" \"$fileStoragePath\" 
				#echo \"Done! \$file\\n\"
			fi
		done
	fi
done";

	if( file_put_contents("spoolManager.sh", $shellContent) == false ) {
		throw new Exception("Unable to create new file");
	}

/*
	THE FOLLOWING EXCERPT WAS TAKEN FROM http://stackoverflow.com/questions/9593724/php-how-to-execute-a-command
	IT IS USED IN THIS APPLICATION TO HANDLE PDF CONVERSION BY LIBREOFFICE.

	------------------------------------------------------------------------------------------


	LibreOffice is a pretty big program, has lots of code you don't know about, 
	it generates and updates files in a directory in your $HOME, and is certainly 
	not something you're going to be able to run more than one copy of at a time.

	So instead of having LibreOffice launched by your web server, and subverting 
	Apache's security by running it as a more privileged user than "www-data" or "nobody", 
	you should make a handler.

	First off, verify that you can run your libreoffice ... command line from a terminal. 
	To be extra sure that you don't have any X11 dependencies, run unset DISPLAY (for bash) 
	or unsetenv DISPLAY (for tcsh) in your xterm before you test your command line. 
	Does it break? Fix that problem first. Does it work? Great, then proceed with a handler.

	Your handler, in its simplest form, can be a script that loops forever, checking for "files to handle" 
	in a spool directory, and if it finds them, converts them using libreoffice and puts the resultant file 
	where it can be found by your web app.

	#!/bin/sh

	while sleep 10; do
	  if [ `ls /var/tmp/myspool/ | grep -c '\.xlsx$'` -gt 0 ]; then
	    ls /var/tmp/myspool/*.xlsx | while read file; do
	      /usr/local/bin/libreoffice --headless -convert-to ooxml "$file" yadda yadda
	      if [ $? = 0 ]; then
	        mv "$file" "/var/tmp/myspool/done/
	      fi
	    done
	  fi
	done

	If you don't want the overhead of something "polling" (checking the spool directory every 10 seconds), then 
	you could have your PHP script add a line to a log that gets watched by your handler. For example:

	<?php

	// process form, save file to spool dir
	syslog(LOG_NOTICE, "Saved: " . $filename);

	?>

	Make sure you've configured syslog to store these messages in, say, /var/log/filelog, then your handler can 
	just tail the log.

	#!/bin/sh

	tail -F /var/log/filelog | while read line; do
	  filename="`echo \"$line\" | sed 's/.*Saved: //'`"
	  /usr/local/bin/libreoffice --headless -convert-to ooxml "$file" yadda yadda
	  # etc ... error handling and mv as in the other script
	done
	Get the idea?
*/


?>