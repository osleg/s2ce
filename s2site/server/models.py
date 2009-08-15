from datetime import datetime, timedelta
from django.db import models

class ActiveServerManager(models.Manager):
    def get_query_set(self):
        return super(ActiveServerManager, self).get_query_set().filter(
        	updated__gte=datetime.now() - timedelta(minutes=10))

class Server(models.Model):
	# Fields
    port = models.IntegerField()
    ip = models.CharField(max_length=15)
    max_conn = models.IntegerField()
    num_conn = models.IntegerField()
    name = models.CharField(max_length=50)
    description = models.CharField(max_length=250)
    minlevel = models.IntegerField()
    maxlevel = models.IntegerField()
    official = models.BooleanField()
    login = models.CharField(max_length=50)
    updated = models.DateTimeField(auto_now=True, auto_now_add=True)

	# Managers
    objects = models.Manager()
    active = ActiveServerManager()    
    
    def __unicode__(self):
        return "%s:%s" % (self.ip, self.port)