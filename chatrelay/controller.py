import logging

from chatserver import ChatClientFactory
from irc import RelayBotFactory
from event import Event

LEVELS = {'debug': logging.DEBUG,
          'info': logging.INFO,
          'warning': logging.WARNING,
          'error': logging.ERROR,
          'critical': logging.CRITICAL}

class Controller:
    
    def __init__(self, chat_client, irc_client):
        self.chat_client = chat_client
        self.irc_client = irc_client
        
        # Subscribe to events
        self.irc_client.on_command += self.on_irc_command
        self.chat_client.on_join += self.on_chat_join
        self.chat_client.on_leave += self.on_chat_leave
        self.chat_client.on_message += self.on_chat_message
        
        # Options
        self.show_meta = False

    # Event handling
        
    def on_irc_command(self, msg):
    	logging.info("Received IRC Command: %s" % msg)
    	
    	# !send message
        if msg.startswith("!send "):
            self.chat_client.send_message(msg[5:len(msg)])        
        # !set key value
        elif msg.startswith("!set "):
            args = msg.split(" ")
            # !set meta 1/0
            if args[1] == "meta":
                if args[2] == "1":
                    self.show_meta = True
                elif args[2] == "0":
                    self.show_meta = False
                else:
                	self.irc_client.send_message("Syntax is !set meta 1/0")
            # !set logging level
            if args[1] == "logging":
            	logging.basicConfig(level=LEVELS.get(args[2], logging.NOTSET))
    
    def on_chat_join(self, name):
    	logging.info("Player joined: %s" % name)
        if self.show_meta:
            self.irc_client.send_message("Player join: %s" % name)
            
    def on_chat_leave(self, name):
    	logging.info("Player left: %s" % name)
        if self.show_meta:
            self.irc_client.send_message("Player left: %s" % name)
            
    def on_chat_message(self, nick, msg):
    	logging.info("Received chat message: <%s> %s" % (nick, msg))
        self.irc_client.send_message("<%s> %s" % (nick, msg))