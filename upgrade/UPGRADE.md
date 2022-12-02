Upgrade the OpenXE system.

NOTE:
The upgrade system is for use in LINUX only and needs to have git installed.

1. get files from git
2. run database upgrade

Files in this directory:
UPGRADE.md -> This file
upgrade.php -> The upgrade program

Files in the data subdirectory:
.in_progress.flag -> if this file exists, an upgrade is in progress, system will be locked
db_schema.json -> Contains the nominal database structure
remote.json -> Contains the git remote & branch which should be used for upgrade
