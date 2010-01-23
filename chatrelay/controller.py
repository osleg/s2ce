import logging

from chatserver import ChatClientFactory
from irc import RelayBotFactory
from event import Event

LEVELS = {'debug': logging.DEBUG,
          'info': logging.INFO,
          'warning': logging.WARNING,
          'error': logging.ERROR,
          'critical': logging.CRITICAL}

class AutoMessage:
    
    def __init__(self, seconds, message):
        self.seconds = seconds
        self.message = message

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
        self.auto_messages = {}
        self.auto_nr = 1
        self.filters = {}
        self.filter_nr = 1
        
        # Commands
        self.commands = {
            'send': self.irc_send,
            'set': self.irc_set,
            'whisper': self.irc_whisper,
            'auto': self.irc_auto,
            'check': self.irc_check,
            'list': self.irc_list,
            'filter': self.irc_filter
        }
        
        #Data
        self.online = {}

    # Event handling
        
    def on_irc_command(self, msg):
        logging.info("Received IRC Command: %s" % msg)

        # Parse command name and arguments
        (command, args) = self.split_data(msg)
        command = command[1:len(command)]
        
        if command in self.commands:
            self.commands[command](args)
        else:
            self.irc_client.send_message("Unknown command: %s" % command)

    def on_chat_join(self, name):
        logging.info("Player joined: %s" % name)
        self.online[name] = True
        if self.show_meta:
            self.irc_client.send_message("Player join: %s" % name)
            
    def on_chat_leave(self, name):
        logging.info("Player left: %s" % name)
        self.online[name] = False
        if self.show_meta:
            self.irc_client.send_message("Player left: %s" % name)
            
    def on_chat_message(self, nick, text):
        logging.info("Received chat message: <%s> %s" % (nick, text))
        message = "<%s> %s" % (nick, text)
        for f in self.filters:
            if message.find(f) > -1:
                return
        self.irc_client.send_message(message)
        
    def on_chat_whisper(self, nick, msg):
        logging.info("Received whisper: <%s> %s" % (nick, msg))
        self.irc_client.send_message("[whisper] <%s> %s" % (nick, msg))
        
    # IRC Commands
        
    def irc_send(self, data):
        # !send message
        self.chat_client.send_message(data)
        
    def irc_set(self, data):
        (key, value) = self.split_data(data)
        # !set meta 1/0
        if key == "meta":
            if value == "1":
                logging.info("Enabled meta messages")
                self.irc_client.send_message("Enabled meta messages")
                self.show_meta = True
            elif value == "0":
                logging.info("Disabled meta messages")
                self.irc_client.send_message("Disabled meta messages")
                self.show_meta = False
            else:
                self.irc_client.send_message("Syntax is !set meta 1/0")
        # !set logging level
        if key == "logging":
            if value in LEVELS:
                logging.info("Changing logging level to %s" % value)
                logging.basicConfig(level=LEVELS[value])
                self.irc_client.send_message("Logging level changed")
            else:
                self.irc_client.send_message("Don't know logging level %s" % value)            
            
    def irc_whisper(self, data):
        # !whisper target message
        (target, message) = self.split_data(data)
        self.chat_client.send_whisper(target, message)
        
    def irc_check(self, name):
        if name in self.online.keys() and self.online[name]:
            self.irc_client.send_message(name + " is online")
        else:
            self.irc_client.send_message(name + " is not online")
    
    def irc_list(self, data):
        self.irc_client.send_message("Online are " + " ,".join([x for x in self.online.keys() if self.online[x]]))
        
    def irc_filter(self, data):
        # !filter command param
        (command, param) = self.split_data(data)
        
        if command == "list":
            self.irc_client.send_message("There are %s filters active" % len(self.filters))
            for nr in self.filters:
                self.irc_client.send_message("%s: %s" % (nr, self.filters[nr]))
        if command == "delete":
            try:
                nr = int(param)
                del self.filters[nr]
                self.irc_client.send_message("Successfully deleted filter %s" % nr)
            except:
                self.irc_client.send_message("Could not find filter nr %s" % param)
        if command == "add":
            try:
                self.filters[self.filter_nr] = param
                self.irc_client.send_message("Added as filter nr %s" % self.filter_nr)
                self.filter_nr += 1
            except:
                self.irc_client.send_message("Syntax is: !filter add [keyword]")
        
    def irc_auto(self, data):
        # !auto command param
        (command, param) = self.split_data(data)
        
        if command == "list":
            self.irc_client.send_message("There are %s auto messages active" % len(self.auto_messages))
            for nr in self.auto_messages:
            	msg = self.auto_messages[nr]
                self.irc_client.send_message("%s: [%s sec] %s" % (nr, msg.seconds, msg.message))
        if command == "delete":
            try:
                nr = int(param)
                del self.auto_messages[nr]
                self.irc_client.send_message("Successfully deleted message %s" % nr)
            except:
                self.irc_client.send_message("Could not find message nr %s" % param)
        if command == "add":
            (seconds, message) = self.split_data(param)
            try:
            	seconds = int(seconds)
            	self.auto_messages[self.auto_nr] = AutoMessage(seconds, message)
            	self.irc_client.send_message("Added as message nr %s" % self.auto_nr)
            	self.auto_nr += 1
            except:
                self.irc_client.send_message("Syntax is: !auto add [seconds] [message]")
                
    # Helpers
    
    def split_data(self, data):
        if data.find(" ") == -1:
            return (data, "")
        else:
            return data.split(" ", 1)