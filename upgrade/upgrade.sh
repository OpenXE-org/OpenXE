#!/bin/bash

echo_user() {
    echo
    echo "--------------- Starting with user \"$_user\" ---------------"
}

while :
do
    clear
    echo "--------------- OpenXE manual upgrade ---------------"
    echo -e "Choose user:"
    echo -e "1) autodect user                           "
    echo -e "2) use current user                        "
    echo -e "3) read username from \"upgrade.user\" file"
    echo -e "4) enter username                          "
    echo ""
    echo -e "0) Exit"
    read  -p "Choose an option: " main </dev/tty
    case $main in
        1)
        _user=$(ls -la data/upgrade.php | awk '{print $3}')
        echo_user
        sudo -u $_user php data/upgrade.php "$@"        
        exit 1
        ;;
        2)
        _user=$(whoami)
        echo_user
        php data/upgrade.php "$@"
        exit 1
        ;;
        3)
        _user=$(cat upgrade.user)
        echo_user
        sudo -u $_user php data/upgrade.php "$@"
        exit 1
        ;;
        4)
        read -p "Enter your username: " _user
        echo_user
        sudo -u $_user php data/upgrade.php "$@"
        exit 1
        ;;
        0)
        exit 0
        ;;
        *)
        clear
        ;;
    esac
done
