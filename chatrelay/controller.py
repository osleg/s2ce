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
        
        # Commands
        self.commands = {
        	'send': self.irc_send,
        	'set': self.irc_set,
        }

    # Event handling
        
    def on_irc_command(self, msg):
    	logging.info("Received IRC Command: %s" % msg)

		# Parse command name and arguments
    	(command, args) = msg.split(" ", 1)
    	command = command[1:len(command)]
    	
    	if command in self.commands:
    		self.commands[command](args)
    	else:
    		self.irc_client.send_message("Unknown command: %s" % command)

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
        
    # IRC Commands
        
    def irc_send(self, data):
    	# !send message
    	self.chat_client.send_message(msg[5:len(msg)])
    	
    def irc_set(self, data):
        args = data.split(" ")
        # !set meta 1/0
        if args[0] == "meta":
            if args[1] == "1":
                self.show_meta = True
            elif args[1] == "0":
                self.show_meta = False
            else:
            	self.irc_client.send_message("Syntax is !set meta 1/0")
        # !set logging level
        if args[1] == "logging":
        	logging.basicConfig(level=LEVELS.get(args[2], logging.NOTSET))    	