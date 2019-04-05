# corda
The Community ORCID Dashboard is a project to bring together multiple sources of institutional and open data around ORCID iD and rationalise them into one central reporting and visualisation offering 

# DemoConnector.php
Two endpoints: 
- http://63.35.7.234/corda-restler/public/corda/index.php/DemoConnector/connect_eprints
- http://63.35.7.234/corda-restler/public/corda/index.php/DemoConnector/connect_haplo

These will make requests to Haplo and Eprints APIs, and return the JSON of these requests and place them into `data/Eprints.json` and `data/Haplo.json` respectively. Use this to get some data up and started for the Downloader methods. 

# Downloader.php
Two endpoints:
- http://63.35.7.234/corda-restler/public/corda/index.php/Downloader/PrintOutput
- http://63.35.7.234/corda-restler/public/corda/index.php/Downloader/ReturnIDList

## ReturnIDList
This endpoint will go through both the `data/Haplo.json` and `data/Eprints.json` files in the data directory. If any users have an ORCID, then it will attempt to make a request to the ORCID API to retrieve additional information regarding their employment etc... 

Will collect all of this data, and combine it into one large JSON format, then dump it as `data/output.json`

## PrintOutput
This endpoint will run through the `data/output.json` file and convert it into a html table. 
