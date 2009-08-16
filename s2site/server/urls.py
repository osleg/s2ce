from django.conf.urls.defaults import *
from s2site.server.views import *

urlpatterns = patterns('',
	url(r'^list/$', list, name="server_list"),        
)