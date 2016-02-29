#!/usr/bin/python
 
#######################################################################
# NAS suspend script                                                  #
# source: http://ubuntuforums.org/showthread.php?t=1423030            #
# https://github.com/andersruberg/htpc-suspend/blob/master/suspend.py #
#######################################################################
#
# The purpose of this script is to suspend the machine if it's idle.
# the original script checks the following but has been amended to simplify for my needs:
# - if a lockfile is present (this way the script can be bypassed when needed)
# - if XBMC is running, and if it's playing [commented out as only applicable to playing content loccally on NAS]
# - if there is keyboard or mouse activity 
# - if transmission download speed is too low to keep the system awake for
# - if there are samba shares in use
# - script will also backup specified files on a schedule [commented out - already have own solution]
# - script will run flexget media sorted/info on a schedule [commented out for now as already have alternate solution]
#
# To function properly this script needs a couple things:
# - from apt-get: xprintidle
# - from apt-get: transmission-cli
# - the gnome-power-control script (installed in /usr/bin) from AgenT: http://ubuntuforums.org/showpost.php?p=8309702&postcount=16
# - xbmc web server enabled without a password (can be found in xmbc settings under network) 
#     (if you don't need xbmc, you can comment out the xbmc section or put the xbmcDelay on 0)
# - to be able to use "sudo smbstatus" (so rootaccess, or if you run the script on userlevel this can be fixed by adding
#     "your_username ALL=(ALL) NOPASSWD: /usr/bin/smbstatus" visudo (run "sudo visudo" in terminal))
# the same goes for sudo /usr/sbin/pm-suspend !!!!! the suspend command!
#
# Ryan:
# Original script in this folder. Removed unused lines to make it clearer and cleaner for my needs.
# If I've 'updated' lines for my needs I've generally commented with three ###'s
#######################################################################

# Check for 'sleep' line below to set how frequently script loops. Shorten for testing.

#########
# DEBUG #
#########
# change this to False if you want the script to output debug info and allow system suspend
debugmode = True
allowsuspend = True

###################
# SCRIPT SETTINGS #
###################

##### DELAY VALUES #####
# the system suspends only if all the different items are idle for a longer time than specified for it
# the values are in minutes, unless you change the sleep command timing
sambafileDelay = 10
transmissionDelay = 5

##### LOCKFILE SETTINGS #####
# this is the path to the lockfile you can use.
# to lock the system, just use the touchcommand: "touch /home/media/.suspendlockfile" (or create a starter to it)
# to unlock the system, just use rmcommand "rm /home/media/.suspendlockfile" (or create a starter to it)
lockfilelist = [
'/raid/shared/scratchpad/_suspendlockfile'
]

##### TRANSMISSION CHECK SETTINGS #####
# command to contact the transmission server, -n is user/pass
### transmissionAdress = "transmission-remote -n USERNAME:PASSWORD "
transmissionAdress = "transmission-remote "
# minimal download speed required to keep the system awake
transmissionSpeed = 50.0

##### SAMBACHECK SETTINGS #######
# the script checks the output of sudo smbstatus to see if there are locked files 
# (usually someone downloading or playing media from the system)
# if no locked files are found, it checks if there are folders in use that should keep the system awake
# 
# smbimportantlist is a list of strings the sambashare should check if they are in use (for instance a documents folder)
# to find out what to put here: 
# 1. connect to the share with another computer
# 2. use "sudo smbstatus" in console
# 3. check for the name of the share, it's printed out under "Service"
#
# makeup of the list is: [ 'item1' , 'item2' , 'item3' ]
#smbimportantlist = [ 'Media' , 'Video' , 'Audio' , 'SteamLibrary' , 'scratchpad' , '/raid/shared' , '/mnt/aufs-shared' , '/shared' ]
smbimportantlist = [
'shared'
]


################
# SCRIPT START #
################

from os import *
from urllib2 import *
from time import sleep
from datetime import *
from sys import argv
import io
import json
import httplib
import sys
import subprocess

#'Zero' variables
sambaIdletime = 0
transmissionIdletime = 0
Lockfile = 0
keeponrunnin = True
sshActive = False

# this is the loop that keeps the script running. the sleep command makes it wait one minute
# if you want to increase/decrease the frequencies of the checks, change the sleeptimer.
# keep in mind that this will change the Delay times for xbmc and samba
while keeponrunnin:
    print "\n \n ==================================================== \n"
    print "Suspend on idle script looping..."
    if debugmode:
        print "~~~ debug mode (system won't suspend) ~~~\n \n"

    sleep(60)# this is the delay that runs the script every minute, lower this value for testing!
#    sleep(5)# testing delay :)

    # open file to write output to then empty it
    f = io.open( '/shared/Webpages/ApacheDocRoot/state/suspend.txt', 'wb' )
    f.truncate()
        
##### SSH CHECK #####
# Check if any ssh connections are open to the server
    ps = subprocess.Popen('netstat -n --protocol inet | grep \':22\'', shell=True, stdout=subprocess.PIPE)
    i=0
    for line in ps.stdout:
        i+=1
    if i:
        sshActive = True
        if debugmode:
            print "===> SSH Connection Open <==="
    else:
        sshActive = False
        if debugmode:
            print "===> No SSH Connections <===" 

            
##### LOCKFILE CHECK #####
# counting the number of lockfiles
    Lockfile = 0
    for i in lockfilelist:
        if path.exists(i):
            Lockfile += 1
            if debugmode:
                print "===> Lockfile found <==="

                
##### SAMBA CHECK #####
    f.write("===> SAMBA <===<br>")
    sambaImportant = False 
    sambaLocked = False 
    try: sambainfo = popen('sudo smbstatus').read()
    except IOError, e:
        if debugmode:
            print "No Sambaserver found, or no sudorights for smbstatus"
        sambaIdletime += 1 
    else:
        # first we check for file-locks
        if sambainfo.find('Locked files:\n') >= 0:
            #sambaIdletime = 0 
            sambaLocked = True 
            if debugmode:
                print "SAMBA locked file has been found (may not be accurate!)"
        # if no locked files, strip the info and look for keywords, if found reset idletime
        else:
            #sambaIdletime += 1
            sambaLocked = False
#section below checks for locked shares in the 'smgimportantlist' - doesn't add any value over just checking locks above
        sambasplit = sambainfo.split('\n')[4:-4]
        sambasplit.reverse()
        for i in sambasplit:
            if i == '-------------------------------------------------------':
                break
            for j in smbimportantlist:
                # check to filter out crashes on empty lines
                if len(i.split()) >= 1:
                    if i.split()[0].find(j) >= 0:
                        #sambaIdletime = 0
                        sambaImportant = True
                        if debugmode:
                            print "an important samba share is in use"

    if sambaImportant == True:
        print "SAMBA - important files are in use - suspend not allowed"
        f.write("SAMBA - important files are in use - suspend not allowed<br>")
        sambaIdletime = 0
    else:
        print "SAMBA - important files are NOT in use - suspend allowed"
        f.write("SAMBA - important files are NOT in use - suspend allowed<br>")
        sambaIdletime += 1


##### TRANSMISSION CHECK #####
# this is the check for torrent activity. it checks the last value in the last line
# from the transmission-remote command, which is the downloadspeed in kb/s
    try: transmissioninfo = popen(transmissionAdress + "-l").read()
    except IOError, e:
        if debugmode:
            print "===> Transmission not installed <==="
        transmissionIdletime += 1
    else: 
        if transmissioninfo == '':
            transmissionIdletime += 1
            if debugmode:
                print "===> Transmission not running <==="
        elif float(transmissioninfo.split()[-1]) >= transmissionSpeed:
                transmissionIdletime = 0
                if debugmode:
                    print "===> transmission is downloading @ %s kb/s <===" %(transmissioninfo.split()[-1])
        else:
            transmissionIdletime += 1


##### TESTS PASSED? #####
# this is the final check to see if the system can suspend. 
# uncomment the print statements and run in terminal if you want to debug/test the script
###    if xbmcIdletime >= xbmcDelay and xIdletime >= xDelay and sambaIdletime >= sambafileDelay and transmissionIdletime >= transmissionDelay and Lockfile == 0:
    if sambaIdletime >= sambafileDelay and transmissionIdletime >= transmissionDelay and Lockfile == 0: #and sshActive == False:
        sambaIdletime = 0
        transmissionIdletime = 0
        if debugmode:
            print "\n ---------------------\n"
            print "Time:", datetime.now()
            print "System inactive - SUSPENDING"
            f.write("---------------------<br>")
            f.write("Time: " + str(datetime.now()) + "<br>")
            f.write("System inactive - SUSPENDING<br>")
            if allowsuspend == True: #use this if you want suspend to be contingent on 'allowsuspend' variable, not debugmode, also uncomment if statement below
                print "allowsuspend set to TRUE - SUSPENDING"
                f.write("allowsuspend set to TRUE - SUSPENDING<br>")
                system('sh /home/ryan/scripts/suspend_on_idle/cmds_pre_sleep.sh')
        else: #uncomment below if you want suspend contingent on 'debugmode' setting
            # The 'cmds_pre_sleep' script includes the hibernate/suspend/shutdown command now
            #system('sh /home/ryan/scripts/suspend_on_idle/cmds_pre_sleep.sh') #Suspend (S3) [USE THIS ONE UNLESS REASON NOT TO]
            #system('sudo /usr/sbin/pm-suspend') #Direct suspend (S3)
            #system('sh /home/ryan/scripts/suspend_on_idle/cmds_pre_sleep.sh -hibernate') #Hibernate (S4)
            #system('sudo /usr/sbin/pm-hibernate') #Direct hibernate (S4)
            #system('sudo /usr/bin/acpitool -S') #Using apt-get package 'acpitool', this hibernates (S4)
            #system('sh /home/ryan/scripts/suspend_on_idle/cmds_pre_sleep.sh -shutdown') #Shutdown (S5) - wake on LAN still works
            #system('sudo /sbin/shutdown -P now') #Direct shutdown (S5) - wake on LAN still works
            if allowsuspend == True: #use this if you want suspend to be contingent on 'allowsuspend' variable, not debugmode, also uncomment if statement above
                print "allowsuspend set to TRUE - SUSPENDING"
                f.write("allowsuspend set to TRUE - SUSPENDING<br>")
								# This command runs another script which is the one where the suspend command is actually issued. But there are various other things I want to do 
								# before suspending. e.g. close down torrent client, disconnect VPN,...
                system('sh /home/ryan/scripts/suspend_on_idle/cmds_pre_sleep.sh')
    else:
        if debugmode:
            print "\n ---------------------\n"
            print "Time: ", datetime.now()
            print "System is active, not suspending"
            print "SAMBA is idle for ", sambaIdletime, " minutes/loops"
            print "Transmission is idle for ", transmissionIdletime, " minutes/loops"
            f.write("---------------------<br>")
            f.write("Time:" + str(datetime.now()) + "<br>")
            f.write("System is active, not suspending<br>")
            f.write("SAMBA is idle for " + str(sambaIdletime) + " minutes/loops<br>")
            f.write("Transmission is idle for " + str(transmissionIdletime) + " minutes/loops<br>")
            if Lockfile == 0:
                print "There is no lock file found"
                f.write("There is no lock file found<br>")
            else:
                print "%i lockfile(s) found" %(Lockfile)
                f.write("Lockfile found<br>")
            if sshActive == False:
                print "There is no SSH session active"
                f.write("There is no SSH session active<br>")
            else:
                print "SSH session active (not set as a blocker for suspending system)"
                f.write("SSH session active (not set as a blocker for suspending system)<br>")

    f.close()
