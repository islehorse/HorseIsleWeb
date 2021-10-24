# HorseIsleWeb
* Note this is part of a larger Horse Isle 1 "Server Emulator" project, the main repo is at https://github.com/IsleHorse/HISP *

This repository is meant to re-implement the server-side script's for horse isle web site(s)
(specifically based on: https://master.horseisle.com and https://pinto.horseisle.com)

# What are the folders?
master-site/ - Contains registration, fourms, help center, server list, and so on (based on master.horseisle.com)          
game-site/ - Contains the game client itself, and minimal PHP scripts to display the account page (based on pinto.horseisle.com)         


# Configuration
to configure your own server there are 3 main files you need to be aware of:
- game-site/config.php - Configuration file for a specific game-site
- master-site/config.php - Configuration file for the main master-site
- master-site/servers.php - Configuration file for server list

Note: HMAC_SECRET must match in master-site and all game-sites,
