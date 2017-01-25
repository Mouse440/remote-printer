#!/bin/sh

# This shell script is a polling script that would convert any supported file present inside the spool directory into pdf.
# It is an alternative solution to executing libreoffice as www-data. What this script will do is, it will infinitely loop
# and execute any file that is present with the supported extension, then spit the output to the storage directory; while 
# also moving the original file to the storage directory.

# NOTE: This script must be recreated from spoolManangerCreator.php everytime an extension support list is updated in Config.php

#Check if spoolManagerPID.txt exist and has size > 0
if [ -s /var/www/sce.engr.sjsu.edu/public_html/services/modules/printing/spool_setup/spoolManagerPID.txt ]; then
	#kill the previous process if pid exist in user process space
	PID=`cat /var/www/sce.engr.sjsu.edu/public_html/services/modules/printing/spool_setup/spoolManagerPID.txt`
	if [ `ps -a | grep -c $PID` -gt 0 ]; then 
		kill $PID #kill process
	fi
fi


#save the current process id to spoolmanagerPID.txt
echo $$ > spoolManagerPID.txt

#Infinite loop, polling
while true 
do 
	# Check for files with supported extensions
	if [ `ls /var/www/sce.engr.sjsu.edu/public_html/services/modules/printing/spool/ | grep -c '\.jpg$\|\.png$\|\.doc$\|\.docx$\|\.ppt$\|\.pptx$\|\.odt$'` -gt 0 ]; then
		# Get those files and loop thru
		ls -dR /var/www/sce.engr.sjsu.edu/public_html/services/modules/printing/spool/* | grep '\.jpg$\|\.png$\|\.doc$\|\.docx$\|\.ppt$\|\.pptx$\|\.odt$' | while read file; do
			#convert file and store output in storage
			libreoffice --headless --convert-to pdf --outdir /var/www/sce.engr.sjsu.edu/public_html/services/modules/printing/temporary_file_storage/ $file 
			#move original file to storage
			if [ $? = 0 ]; then
				mv "$file" "/var/www/sce.engr.sjsu.edu/public_html/services/modules/printing/temporary_file_storage/" 
				#echo "Done! $file\n"
			fi
		done
	fi
done