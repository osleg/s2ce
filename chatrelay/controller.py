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
        self.chat_client.on_whisper += self.on_chat_whisper        
        
        # Options
        self.show_meta = False
        
        # Commands
        self.commands = {
        	'send': self.irc_send,
        	'set': self.irc_set,
        	'whisper': self.irc_whisper,
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
        
    def on_chat_whisper(self, nick, msg):
    	logging.info("Received whisper: <%s> %s" % (source, msg))
    	self.irc_client.send_message("[whisper] <%s> %s" % (nick, msg))
        
    # IRC Commands
        
    def irc_send(self, data):
    	# !send message
    	self.chat_client.send_message(msg[5:len(msg)])
    	
    def irc_set(self, data):
        (key, value) = data.split(" ", 1)
        # !set meta 1/0
        if key == "meta":
            if value == "1":
                self.show_meta = True
            elif value == "0":
                self.show_meta = False
            else:
            	self.irc_client.send_message("Syntax is !set meta 1/0")
        # !set logging level
        if key == "logging":
        	logging.basicConfig(level=LEVELS.get(value, logging.NOTSET))    	
        	
	def irc_whisper(self, data):
		# !whisper target message
		(target, message) = data.split(" ", 1)
		self.chat_client.send_whisper(target, message)