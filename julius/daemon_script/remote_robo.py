#!/usr/bin/env python
# -*- coding: utf-8 -*-
import socket
import subprocess
#import time

host = 'localhost'
port = 10500

def music_on():
    # music on
    cmd ="./play_music_pi.sh"
    subprocess.call(cmd, shell=True)

def music_off():
    # music_off
    cmd ="./play_music_pi.sh -stop"
    subprocess.call(cmd, shell=True)

def take_photo():
    # take photo
    cmd ="./take_photo_julius.sh"
    subprocess.call(cmd, shell=True)

# connect julius
clientsock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
clientsock.connect((host, port))

sf = clientsock.makefile('rb')

while True:
    line = sf.readline().decode('utf-8')
    if line.find('WHYPO') != -1:
        print line
        if line.find(u'music_on') != -1:
            print("music_on")
            music_on()
        elif line.find(u'music_off') != -1:
            print("music_off")
            music_off()
	elif line.find(u'take_photo') != -1:
            print("take_photo")
            take_photo()


