OpenXE upgrade system

NOTE:
The upgrade system is for use in LINUX only and needs to have git installed.

The following steps are executed:
1. get files from git
2. run database upgrade

Files in this directory:
UPGRADE.md -> This file
upgrade.sh -> The upgrade starter, execute with "./upgrade.sh". Execute without parameters to view possible options.

Files in the data subdirectory:
upgrade.php -> The upgrade program
db_schema.json -> Contains the nominal database structure
exported_db_schema.json -> Contains the exported database structure (optional)
remote.json -> Contains the git remote & branch which should be used for upgrade
upgrade.log -> Contains the output from the last run that was started from within OpenXE
