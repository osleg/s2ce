from django.conf.urls.defaults import *
from s2site.player.views import *

urlpatterns = patterns('',	
	url(r'^register/$', register, name="player_register"),
	url(r'^profile/$', profile, name="player_profile"),
)