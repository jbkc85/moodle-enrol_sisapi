Installation Instructions
========

# Install Plugin on your Server

1) Download or Clone the file from https://github.com/jbkc85/moodle-enrol_sisapi into your MOODLEDIR/enrol folder

https://github.com/jbkc85/moodle-enrol_sisapi/archive/master.zip

OR 

* git clone https://github.com/jbkc85/moodle-enrol_sisapi.git sisapi
* cd sisapi
* git submodule init
* git submodule update

2) Move the plugin into "enrol/sisapi" (already done if you copy pasted the git clone)

# Install Plugin in Moodle

1) Login as your Site Administrator
2) Install Plugin from Notification
3) Select a Library (Clever, LearnSprout)
4) Fill in the necessary information

Testing Information
======

## SISAPI Settings for LearnSprout
* API Library-> LearnSprout
* API URL-> https://v1.api.learnsprout.com/
* API Key-> fcb8534c-e4ee-4e02-8b22-9328db1dac18
* Org ID -> 506b8b1f780aa79602388b42

### Testing LearnSprout (Create this user)
* User: wstehr
* ID Number: 506bc8800e130a4b4b919424
* email: wstehr@learnsprout.example.com
* pass: Changem3!

## SISAPI Settings for Clever:
* API Library-> Clever
* API URL-> https://api.getclever.com/v1.1/
* API Key-> DEMO_KEY
* Enable SSL by Default -> TRUE

### Testing Clever (Create this user)
* user: anita.morar
* ID Number: 4fee004cca2e43cf2700000b
* email: anita.morar@mailinator.com
* pass: Changem3!

Author Information
======
* Jason Cameron <jbkc85@gmail.com>
