#!/usr/bin/python
# -*- coding: utf-8 -*-

from xbmcjson import XBMC
from pprint import pprint 
import random
import sys
import argparse

x = XBMC("http://127.0.0.1/jsonrpc")

def get_music_genre():
  result = x.AudioLibrary.GetGenres()
  return result.get("result", [])

def set_volume(vol):
  x.Application.SetVolume(volume=vol)

def get_active_players():
  result = x.Player.GetActivePlayers()
  return result.get("result", [])

def play_file(filepath):
  x.Player.Open({"item": {"file": filepath}})

def playlist_add_genre(id):
  x.Playlist.Add({"playlistid":1, "item" :{"genreid" : id}})

def playlist_clear():
  x.Playlist.Clear({"playlistid":1})

def playlist_getitems():
  x.Playlist.GetItems({"playlistid":1})

def player_open():
  x.Player.Open({"item" :{"playlistid":1, "position" : 0}})

def play_suffle():
  players = get_active_players()
  for player in players:
    x.Player.SetShuffle({"playerid": player["playerid"], "shuffle":1})

def play_pause():
  players = get_active_players()
  for player in players:
    x.Player.PlayPause({"playerid": player["playerid"]})

def play_stop():
  players = get_active_players()
  for player in players:
    x.Player.Stop({"playerid": player["playerid"]})

def is_play_video():
  players = get_active_players()
  for player in players:
    type = (player["type"])
    if type == "video":
      return 1
    else:
      return 0


#引数からplay,stop,pause
parser = argparse.ArgumentParser(description=u"osmc remoteplayer")
parser.add_argument('-stop', const=1, nargs="?",help="play stop")
parser.add_argument('-pause', const=1, nargs="?",help="play pause")
args = parser.parse_args()

# 引数を見る
if args.stop == 1:
  print u"stop"
  play_stop()
elif args.pause == 1:
  print u"pause"
  play_pause()
else:
  #ビデオ再生中は音楽をかけない
  if is_play_video() == 1:
    print u"now playing video. exit..."
    exit()
  else:
    print u"play music"

    #ボリュームを40%に
    set_volume(40)

    # ジャンルをランダムで
    genre_id = random.randint(1,39)
    # 除外ジャンルリスト
    ex_glist = [5, 8, 10, 11, 19, 20, 22, 35, 37, 38, 39]
    if genre_id in ex_glist:
      # ピックアップジャンルリスト
      pickup_g = [2, 3, 6, 7, 9, 14, 16]
      genre_id = random.choice(pickup_g)

    # jsonで命令を投げる
#    print u"stop"
#    play_stop()
    print u"list clear"
    playlist_clear()
    print u"list sufule"
    play_suffle()
    print u"add gunre:" + str(genre_id)
    playlist_add_genre(genre_id)
    print u"player open"
    player_open()
