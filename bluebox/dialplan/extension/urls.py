from django.conf.urls import patterns, include, url

from .views import *

urlpatterns = patterns('',
    url(r'^create/$', create, name='create'),
    url(r'^conference/', include('bluebox.dialplan.extension.conference.urls', namespace='conference')),
    url(r'^list/$', list_extension, name='extension'),
    url(r'^voicemail/(?P<file_id>[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/', include('bluebox.dialplan.extension.voicemail.urls', namespace='voicemail')) 
)   