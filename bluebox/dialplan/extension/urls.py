from django.conf.urls import patterns, include, url

from .views import *

urlpatterns = patterns('',
    url(r'^create/$', create, name='create'),
    url(r'^conference/', include('bluebox.dialplan.extension.conference.urls', namespace='conference'))
)   