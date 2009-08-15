from django.conf.urls.defaults import *
from django.conf import settings
from django.contrib import admin
from django.views.generic.simple import direct_to_template
import os

# Administration
admin.autodiscover()

# Main patterns
urlpatterns = patterns('',
        (r'^admin/', include(admin.site.urls)),
)


# Static files (when debug is active)
if settings.DEBUG:
        urlpatterns += patterns('django.views.static',
                (r'^media/(?P<path>.*)$',
                        'serve', {
                                'document_root': os.path.join(os.path.dirname(__file__), '../media'),
                                'show_indexes': True }),)