from sys import stdout
from struct import pack, unpack

from twisted.internet.protocol import Protocol, ReconnectingClientFactory

from event import Event

# Packet types
PK_LOGIN=0
PK_WELCOME=1
PK_PINGSERVER=2
PK_PINGCLIENT=3
PK_MESSAGE=4
PK_LIST=5
PK_JOIN=6
PK_LEAVE=7
PK_WHISPER=9

# Protocol
class ChatServer(Protocol):
    # Sending packets
    def send_packet(self, number, params):
        data = pack('b', number)
        for val in params:
            if isinstance(val, int):
                data += self.pack_int(val)
            else:
                data += self.pack_string(val)
        self.transport.write(data)
                
    def pack_byte(self, value):
        return chr(value)
    
    def pack_string(self, value):	
        return value + chr(0)
    
    def pack_int(self, value):
        return pack('i', value)
        
    # Receiving data
    def dataReceived(self, data):
        number = unpack('b', data[0])[0]
        
        if number == PK_WELCOME:
            # no data
            self.welcome()
        elif number == PK_PINGSERVER:
            # no data
            self.ping()
        elif number == PK_LIST:
            # not supported at the moment
            pass
        elif number == PK_JOIN:
            # <name><id>
            name = data[1:(len(data)-5)]
            id = unpack('i', data[len(data)-4:len(data)])[0]
            
            self.join(name, id)
        elif number == PK_LEAVE:
            # <id>
            id = unpack('i', data[1:5])[0]
            
            self.leave(id)
        elif number == PK_MESSAGE:
            # <id><message>
            id = unpack('i', data[1:5])[0]
            offset = data.find(chr(0), 5)
            message = data[5:offset]
            
            self.message(id, message)
        elif number == PK_WHISPER:
            # <nick><message>
            offset = data.find(chr(0))
            nick = data[0:offset]
            end = data.find(chr(0), offset + 1)
            message = data[offset+1:end]
            
            self.whisper(id, nick, message)
        else:
            print "Received unknown packet: %s" % number

    # Callbacks for indiviual packets
    def welcome(self):
        print "Received welcome packet";
    
    def message(self, id, text):
        print "Received message: %s: %s" % (id, text)
        
    def whisper(self, source, text):
        print "Received whisper: %s: %s" % (source, text)
        
    def join(self, name, id):
        print "User joined: %s (%s)" % (name, id)
        
    def leave(self, id):
        print "User left: %s" % id
        
    def ping(self):
        print "Ping? Pong!"

# Basic Implementation
class ChatServerClient(ChatServer):

    # Connection management
    def connectionMade(self):
        self.send_packet(PK_LOGIN, [self.factory.account_id, self.factory.token])

    # Answer callbacks
    def join(self, name, id):
        self.factory.users[id] = name
        
    def ping(self):
        self.send_packet(PK_PINGCLIENT, [])
        
    def leave(self, id):
        pass

    def message(self, id, text):
        pass
        
    def whisper(self, source, text):
        pass
        
    def welcome(self):
        pass

    # Resolve names
    def get_user_name(self, id):
        if id in self.factory.users:
            return self.factory.users[id]
        else:
            return "%s" % id	
        
    # Send public and private message
    def send_message(self, message):
        self.send_packet(PK_MESSAGE, [message])
    
    def send_whisper(self, target, message):
        self.send_packet(PK_WHISPER, [target, message])


# Implementation supporting events and communication with factory
class ChatServerEventClient(ChatServerClient):
    
    # Connection management
    def connectionMade(self):
        ChatServerClient.connectionMade(self)
        self.factory.echoers.append(self)
        
    def connectionLost(self, reason):
        ChatServerClient.connectionLost(self)
        self.factory.echoers.remove(self)               
    
    # Mapping callbacks to events
    def join(self, name, id):
        ChatServerClient.join(self, name, id)
        self.factory.on_join(name)
    
    def leave(self, id):
        ChatServerClient.leave(self, id)
        self.factory.on_leave(self.get_user_name(id))
        
    def message(self, id, message):
        ChatServerClient.message(self, id, message)
        self.factory.on_message(self.get_user_name(id), message)
        
    def whisper(self, source, message):
        ChatServerClient.whisper(self, source, message)
        self.factory.on_whisper(source, message)
    
# Client factory
class ChatClientFactory(ReconnectingClientFactory):
        
    protocol = ChatServerEventClient
    
    def __init__(self, token, account_id):
        # Persistent data
        self.token = token
        self.account_id = account_id
        self.echoers = []
        self.users = {}
        
        # Events
        self.on_join = Event()
        self.on_leave = Event()
        self.on_message = Event()
        self.on_whisper = Event()

    # Send messages        
    def send_message(self, message):
        for echoer in self.echoers:
            echoer.send_message(message)
            
    def send_whisper(self, target, message):
        for echoer in self.echoers:
            echoer.send_whisper(target, message)